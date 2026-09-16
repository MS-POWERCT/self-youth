# TapTap 登录接入准备清单

> 适用项目：`self-youth`（Laravel 9 + Passport + `user_identities` 身份表）  
> 预留 Provider：`taptap`（见 `App\Models\UserIdentity::PROVIDER_TAPTAP`）

---

## 一、TapTap 开发者平台（必做）

1. 注册/登录 [TapTap 开发者中心](https://developer.taptap.cn/)
2. **创建游戏/应用**，获取：
   - `Client ID`（应用 ID）
   - `Client Token`（**仅服务端使用，禁止写入 App**）
3. **开通「TapTap 登录」**，确认授权范围至少包含：
   - `basic_info` 或 `public_profile`（获取昵称、头像）
4. **确认目标市场**（国内/海外接口不同）：
   - 国内：`https://open.tapapis.cn/...`
   - 海外：`https://openapi.tap.io/...`
5. 按文档配置 **包名 / Bundle ID / 应用签名** 等客户端信息

---

## 二、客户端 App（必做）

TapTap 登录是 **SDK 先授权 → 服务端验 Token**，流程类似 Web3，不是纯后端接口。

客户端需要：

1. 集成 **TapTap Login SDK**（iOS / Android 各平台文档）
2. 用户授权后，从 SDK 获取 **Access Token**，包含：
   - `kid` / `access_token`
   - `mac_key`
   - `mac_algorithm`（通常 `hmac-sha-1`）
   - `scope` / `scopes`
3. 将 Token **POST 到自有后端**（HTTPS），不要在客户端自行调用 TapTap 用户信息接口完成「最终登录」
4. 每次登录从 SDK 获取 **最新 Token**（TapTap 不建议长期缓存 Token）

---

## 三、服务端架构（本项目已有基础）

| 已有能力 | TapTap 对应 |
|---------|-------------|
| `user_identities.provider = taptap` | 已定义常量 |
| `IdentityService::authenticate()` | 验 Token 后 findOrCreate |
| Passport `createToken('api')` | 返回 `access_token` |

**待新增文件（建议）：**

```
app/Services/TapTapLoginService.php           # MAC 签算 + 调 TapTap OpenAPI
app/Http/Middleware/TapTapTokenMiddleware.php # 校验客户端 token 参数
app/Api/Auth/TapTapLoginController.php
routes/api.php → POST /auth/taptap/login
```

**`.env` 预留：**

```env
TAPTAP_CLIENT_ID=
TAPTAP_CLIENT_TOKEN=
TAPTAP_API_HOST=https://open.tapapis.cn
# 海外可改为 https://openapi.tap.io
TAPTAP_PROFILE_PATH=/account/profile/v1
```

---

## 四、身份字段存储规则

写入 `user_identities` 表：

| 字段 | TapTap 存什么 |
|------|----------------|
| `provider` | `taptap` |
| `identifier` | **`unionid`**（同一开发者下所有 App 唯一，推荐作主键） |
| `metadata` | `openid`、`name`、`avatar`、scope 等 |
| `verified_at` | 服务端验 Token 成功时间 |

说明：

- `openid` 仅在**单个应用**内唯一，建议放 `metadata`
- `unionid` 适合作为跨 App 的统一账号标识

---

## 五、账号绑定策略（开发前需定稿）

项目已有 **邮箱 / Web3** 登录，需明确：

| 场景 | 建议 |
|------|------|
| 纯 TapTap 新用户 | `createUser` + 写入 `taptap` identity |
| 已登录用户绑 TapTap | 新增 `POST /my/bindTapTap`（类似 `bindEmail`） |
| TapTap unionid 已存在 | 直接登录对应 `users.id` |
| 与邮箱/Web3 账号合并 | 是否允许、是否需二次确认 |

---

## 六、安全与中间件

参考现有实现：

- `Web3SignatureMiddleware` — 生产验签，`local` 可跳过
- `EmailLoginCodeMiddleware` — 生产验验证码，`local` 可跳过

TapTap 建议：

- **生产环境**：必须用 `mac_key` 签 MAC Token，再请求 TapTap 用户信息接口；**不可信任**客户端直传的 `unionid`
- **本地环境**：`APP_ENV=local` 可 mock 跳过（便于联调）
- 路由加 `limit_form_repeat:3` 防刷
- `Client Token`、`mac_key` **不要写日志**

### MAC Token 签算流程（概要）

1. 客户端将 `access_token`、`mac_key`、`kid` 发给服务端
2. 服务端按 TapTap 文档拼接待签名字符串（timestamp、nonce、method、URI、host、port）
3. 使用 `mac_key` + HMAC-SHA1 生成 `mac`
4. 构造 Header：`Authorization: MAC id="...",ts="...",nonce="...",mac="..."`
5. 请求用户信息接口，解析 `unionid` / `openid` / 昵称 / 头像

官方文档：[TapTap OAuth / 获取用户信息](https://developer.taptap.cn/docs/sdk/taptap-login/taptap-oauth/)

---

## 七、后台与运营（可选）

1. Admin 用户卡片展示 TapTap 昵称 / unionid
2. 用户列表支持按 `provider = taptap` 筛选
3. 是否需要「解绑 TapTap」能力

---

## 八、测试准备

1. TapTap **测试账号**（开发者中心创建/邀请）
2. **真机 + 正式 Client ID** 联调
3. 用官方 MAC 签算脚本对照服务端结果
4. 覆盖场景：
   - 首次 TapTap 登录（自动注册）
   - 再次登录
   - Token 过期 / 无效
   - 已登录用户绑定 TapTap
   - 多 identity 共存（邮箱 + TapTap + Web3）

---

## 九、推荐落地顺序

```
1. 开发者中心创建应用，拿到 Client ID / Client Token
2. 客户端集成 TapTap SDK，能 POST Access Token 给后端
3. 实现 TapTapLoginService（MAC 签算 + 拉 profile）
4. 接入 IdentityService::authenticate('taptap', $unionid)
5. 新增 POST /api/auth/taptap/login
6. （可选）新增 POST /my/bindTapTap
7. 联调 + Admin 展示
```

---

## 十、与现有代码的对应关系

```text
客户端 TapTap SDK
    ↓ access_token + mac_key + kid
TapTapLoginController@login
    ↓ TapTapTokenMiddleware（验参）
TapTapLoginService::verifyAndGetProfile()
    ↓ 得到 unionid / name / avatar
IdentityService::authenticate('taptap', $unionid)
    ↓ 写 user_identities，发 Passport token
返回 { access_token }   // 与邮箱/Web3 一致
```

---

## 相关文档

- [微信小程序登录接入准备](./wechat-miniprogram-login-prep.md)
- [项目提醒](./项目提醒.md)
