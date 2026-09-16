# 微信小程序登录接入准备清单

> 适用项目：`self-youth`（Laravel 9 + Passport + `user_identities` 身份表）  
> 预留 Provider：`wechat_mp`（见 `App\Models\UserIdentity::PROVIDER_WECHAT_MP`）

---

## 一、微信公众平台（必做）

1. 登录 [微信公众平台](https://mp.weixin.qq.com/)，注册/认证**小程序**
2. 在 **开发 → 开发管理 → 开发设置** 获取：
   - `AppID`（小程序 ID）
   - `AppSecret`（**仅服务端保存，禁止写入小程序前端**）
3. 配置 **服务器域名**（request 合法域名）：
   - 你的 API 域名，如 `https://api.example.com`
   - 需 HTTPS，且已在微信后台备案白名单
4. （可选）若需 **手机号快速验证**：
   - 开通相应能力 / 组件
   - 了解收费与合规要求
5. （可选）若需 **多端统一账号**（小程序 + App + 公众号）：
   - 绑定同一 **微信开放平台** 账号
   - 登录后可获得 **`unionid`**（无开放平台则通常只有 `openid`）

---

## 二、小程序端（必做）

微信登录核心是 **`wx.login` 换 `code`，由服务端换 `openid`**。

### 标准流程

```text
1. 小程序调用 wx.login() → 得到临时 code（5 分钟有效，仅用一次）
2. 小程序将 code POST 到自有后端
3. 后端请求微信 code2Session 接口 → 得到 openid、session_key
4. （可选）unionid — 需绑定开放平台且用户满足条件
5. 后端 findOrCreate 用户，签发 Passport access_token
6. 返回 access_token 给小程序存储（建议内存 + 本地加密存储，勿明文长期暴露）
```

### 小程序端注意

| 项 | 说明 |
|----|------|
| `code` | 一次性，后端验证后失效，不可复用 |
| `session_key` | **不得下发给前端**，仅服务端短期使用（Redis） |
| 用户昵称/头像 | 微信已调整策略，需用「头像昵称填写能力」或 `open-type` 组件，不能默认静默获取 |
| 手机号 | 需用户主动点击 `getPhoneNumber` 按钮，后端用 `code` 换手机号 |

---

## 三、服务端接口（微信官方）

### code2Session（登录必调）

```
GET https://api.weixin.qq.com/sns/jscode2session
  ?appid=APPID
  &secret=SECRET
  &js_code=CODE
  &grant_type=authorization_code
```

返回（成功）：

```json
{
  "openid": "用户在本小程序唯一标识",
  "session_key": "会话密钥",
  "unionid": "同一开放平台下唯一标识（可能不存在）"
}
```

### 错误码需处理

- `40029` code 无效
- `40163` code 已被使用
- `45011` 频率限制
- `-1` 系统繁忙

---

## 四、本项目架构映射

| 字段 | 微信小程序存什么 |
|------|------------------|
| `provider` | `wechat_mp` |
| `identifier` | **`openid`**（按当前小程序 AppID 维度唯一） |
| `metadata` | `unionid`（有则存）、`appid`、昵称、头像等 |
| `verified_at` | code2Session 成功时间 |

说明：

- **唯一键建议**：`(provider, identifier)` = `(wechat_mp, openid)`
- 若已接入微信开放平台，`unionid` 放 `metadata`，可用于跨端账号打通
- **`session_key` 不要入库**，放 Redis，例如：`wechat_mp:session:{openid}`，TTL 与微信 session 策略一致

**`.env` 预留：**

```env
WECHAT_MP_APPID=
WECHAT_MP_SECRET=
# 若同一主体有多个小程序，可按 app_name header 区分多套配置
```

---

## 五、待新增代码（建议）

```
app/Services/WechatMiniLoginService.php      # code2Session + 解密手机号等
app/Http/Middleware/WechatMiniCodeMiddleware.php
app/Api/Auth/WechatMiniLoginController.php
routes/api.php → POST /auth/wechat/login
config/wechat.php                            # 可选，集中管理 AppID/Secret
```

**登录接口建议参数：**

```json
{
  "code": "wx.login 返回的 code"
}
```

**可选扩展接口：**

| 接口 | 用途 |
|------|------|
| `POST /auth/wechat/login` | code 登录 |
| `POST /my/bindWechat` | 已登录用户绑定微信 |
| `POST /my/wechatPhone` | 绑定手机号（需 phone code） |

---

## 六、账号绑定策略（开发前需定稿）

与现有 **邮箱 / Web3 / TapTap** 并存时需明确：

| 场景 | 建议 |
|------|------|
| 纯微信新用户 | 自动注册 + 写 `wechat_mp` identity |
| 已登录（邮箱/Web3）绑微信 | `bindWechat`，校验 openid 未被占用 |
| 同一 openid 再次登录 | 直接登录原账号 |
| 有 unionid 时跨端合并 | 可选：按 unionid 查找已有用户再绑定 openid |

---

## 七、安全与中间件

参考现有 `Web3SignatureMiddleware` / `EmailLoginCodeMiddleware`：

| 环境 | 策略 |
|------|------|
| `production` | 必须调微信 `jscode2session` 验 code，禁止信任客户端传的 openid |
| `local` | 可 mock 跳过（固定测试 openid），便于开发 |

其他：

- `AppSecret` 仅服务端 `.env`
- `session_key` 存 Redis，过期删除
- 登录接口加 `limit_form_repeat:3`
- 记录微信 errcode，便于排查

---

## 八、与 Passport / 现有 API 对齐

登录成功响应格式与现有接口一致：

```json
{
  "res_code": 0,
  "res_msg": "欢迎...",
  "data": {
    "res_code": 0,
    "res_msg": "...",
    "access_token": "..."
  }
}
```

后续请求 Header：

```
Authorization: Bearer {access_token}
```

---

## 九、测试准备

1. 微信开发者工具 + 真机预览（部分能力仅真机可用）
2. 测试号 / 体验版小程序
3. 服务器域名已在微信后台配置
4. 测试场景：
   - 首次 code 登录（自动注册）
   - 同一用户再次 login
   - code 过期 / 重复使用
   - 已登录用户 bindWechat
   - 无 unionid / 有 unionid 两种情况
   - （可选）手机号绑定

---

## 十、推荐落地顺序

```
1. 小程序后台拿到 AppID / AppSecret，配置 request 合法域名
2. 小程序 wx.login → 把 code 发给后端
3. 实现 WechatMiniLoginService::code2Session()
4. IdentityService::authenticate('wechat_mp', $openid)
5. 新增 POST /api/auth/wechat/login
6. session_key 存 Redis（若后续要解密数据）
7. （可选）bindWechat / 手机号 / 头像昵称同步
8. Admin 展示微信 identity
```

---

## 十一、与现有代码的对应关系

```text
小程序 wx.login()
    ↓ code
WechatMiniLoginController@login
    ↓ WechatMiniCodeMiddleware
WechatMiniLoginService::loginByCode($code)
    ↓ 微信 jscode2session → openid [, unionid]
IdentityService::authenticate('wechat_mp', $openid)
    ↓ metadata 写入 unionid 等
返回 Passport access_token
```

---

## 相关文档

- [TapTap 登录接入准备](./taptap-login-prep.md)
- [项目提醒](./项目提醒.md)
