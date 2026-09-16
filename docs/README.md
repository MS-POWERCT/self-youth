# 项目文档目录

本目录存放 Self Youth 项目的开发、运维与第三方登录接入文档。

## 目录索引

| 文档 | 说明 |
|------|------|
| [核心业务开发文档.md](./核心业务开发文档.md) | 习惯打卡 / 时长记录模块业务与数据表设计 |
| [项目提醒.md](./项目提醒.md) | 备份恢复、Passport 密钥、Google 2FA、邮件 SSL 等 |
| [taptap-login-prep.md](./taptap-login-prep.md) | TapTap 登录接入准备清单 |
| [wechat-miniprogram-login-prep.md](./wechat-miniprogram-login-prep.md) | 微信小程序登录接入准备清单 |

## 认证体系说明

用户登录身份统一存储在 `user_identities` 表，由 `IdentityService` 管理。

| provider | 说明 | 状态 |
|----------|------|------|
| `email` | 邮箱验证码 / 密码 | 已接入 |
| `web3` | 钱包签名 | 已接入 |
| `visitor` | 游客（接口已关闭） | 已停用 |
| `taptap` | TapTap SDK | 待接入 |
| `wechat_mp` | 微信小程序 | 待接入 |

## 外部链接

- [TapTap 开发者文档 - OAuth](https://developer.taptap.cn/docs/sdk/taptap-login/taptap-oauth/)
- [微信小程序 - 登录](https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/login.html)
- [小程序 code2Session](https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/user-login/code2Session.html)
