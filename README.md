# Self Youth

Self Youth 是一款面向自律与生活方式的后端 API 服务，为移动端 App 提供用户认证、习惯打卡、时长记录、标记、情侣圈、体重追踪以及农场小游戏等能力。数据按用户隔离，全部云端存储。

## 技术栈

| 类别 | 技术 |
|------|------|
| 框架 | Laravel 9、PHP 8.0+ |
| HTTP 服务 | [Laraman](https://github.com/itinysun/laraman)（Workerman 常驻进程，默认 `http://127.0.0.1:8000`） |
| API 认证 | Laravel Passport（`auth:api`） |
| 后台管理 | Dcat Admin 2.x（Google 2FA） |
| 数据库 | MySQL |
| 缓存 | Redis / File（可配置） |
| 其他 | Web3 钱包登录、阿里云 OSS、Activity Log |

## 功能模块

### 核心业务

- **习惯打卡** — 布尔型日常习惯，支持周/月统计
- **时长/计数记录** — 数值型自律行为，支持多次累加与历史回溯
- **标记吧** — 分类 / 模块 / 项目标记
- **情侣圈** — 动态发布、互动与评论
- **体重记录** — CRUD、统计与图表（`GET /api/weightRecord/chart?days=90`）

### 农场小游戏

- 土地种植、收获、升级
- 商店购买、仓库管理、任务与 NPC
- 图鉴（Handbook）与静态资源图标

### 用户与认证

- 访客登录（UUID）
- 邮箱验证码登录
- Web3 钱包签名登录（`/api/web3/signature`、`/api/web3/login`）
- 个人信息填写、邮箱/钱包绑定

## 目录结构

```
app/
├── Api/              # 移动端 API 控制器
├── Admin/            # Dcat Admin 后台控制器
├── Models/           # Eloquent 模型
├── Services/         # 业务服务层
└── Support/          # 统一响应等工具

routes/
├── api.php           # API 路由
└── web.php

public/images/farm/
├── icons/            # 农场物品 SVG 图标（65 个）
├── icons/npc/        # 农场 NPC SVG 图标（15 个）
├── farm-icons.xlsx   # 物品图标清单
└── farm-npc-icons.xlsx

scripts/
├── generate_farm_icons.py      # 生成农场物品图标
└── generate_farm_npc_icons.py  # 生成 NPC 图标

config/laraman/       # Laraman 进程配置
```

## 环境要求

- PHP >= 8.0.2，扩展：openssl、pdo_mysql、mbstring、tokenizer、xml、ctype、json、bcmath
- Composer
- MySQL 5.7+
- Redis（推荐，用于缓存/队列）
- Python 3（仅重新生成农场图标时需要）

## 本地开发

### 1. 安装依赖

```bash
cp .env.example .env
composer install
php artisan key:generate
```

### 2. 配置 `.env`

至少配置以下项：

```env
APP_NAME="Self Youth"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=self_youth
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

LARAMAN_WEB_LISTEN=http://127.0.0.1:8000
ADMIN_ROUTE_PREFIX=admin
```

### 3. 数据库与 Passport

```bash
php artisan migrate
php artisan passport:install
# 若密钥已存在需覆盖：
# php artisan passport:keys --force
```

> 从备份恢复项目时，请重新生成 `APP_KEY` 与 Passport 密钥，并重新配置 Admin Google 2FA。详见 [项目提醒.md](./项目提醒.md)。

### 4. 启动服务

**推荐：Laraman（生产/开发均可用）**

```bash
php laraman start          # 启动
php laraman start -d       # 后台运行
php laraman stop           # 停止
php laraman reload         # 平滑重载
php laraman status         # 状态
```

默认监听：`http://127.0.0.1:8000`

**备选：Laravel 内置服务器（开发调试）**

```bash
php artisan serve
```

### 5. 后台管理

访问地址：`http://127.0.0.1:8000/admin`（前缀由 `ADMIN_ROUTE_PREFIX` 控制）

后台涵盖用户、习惯、标记、体重、农场、钱包资产等管理功能。

## API 说明

### 基础路径

所有 API 前缀为 `/api`，例如：

```
GET  /api/global/getInitData
POST /api/auth/visitor/loginVisitor
POST /api/habit/getList          # 需 Bearer Token
```

### 认证方式

登录成功后返回 Passport Access Token，后续请求在 Header 中携带：

```
Authorization: Bearer {access_token}
```

受保护路由使用 `auth:api` 中间件。

### 统一响应格式

```json
{
  "res_code": 0,
  "res_msg": "成功",
  "data": {}
}
```

- `res_code = 0` 表示成功
- 失败时 `res_code` 为非零业务码，`res_msg` 为错误说明
- 封装于 `App\Support\Response::success()` / `Response::error()`

### 常用中间件

| 中间件 | 说明 |
|--------|------|
| `check_uuid` | 纯访客用户（仅有 UUID、未绑定邮箱/钱包）禁止写入类操作 |
| `limit_form_repeat:N` | N 秒内防重复提交 |
| `web3.signature` | Web3 签名验证 |

### 主要 API 分组

| 模块 | 路径前缀 | 说明 |
|------|----------|------|
| 全局 | `/global` | 初始化数据 |
| 认证 | `/auth`, `/web3` | 访客 / 邮箱 / Web3 登录 |
| 用户 | `/my` | 个人信息、绑定、日志 |
| 习惯 | `/habit` | 习惯配置与统计 |
| 打卡 | `/habit/check` | 今日打卡切换 |
| 数值 | `/habit/value` | 时长/计数记录 |
| 标记 | `/mark` | 标记分类与项目 |
| 情侣圈 | `/loverCircle`, `/loverComment` | 动态与评论 |
| 体重 | `/weightRecord` | 记录 CRUD、stats、chart |
| 农场 | `/farmUser`, `/farmShop`, `/farmWarehouse`, `/farmTask` | 农场玩法 |
| 其他 | `/appupdate` | App 版本检查 |

完整路由定义见 [`routes/api.php`](./routes/api.php)。

## 农场静态资源

### 物品图标

- 路径：`public/images/farm/icons/*.svg`
- 清单：`public/images/farm/farm-icons.xlsx`
- 模型解析：`FarmHandbook::resolveIconUrl($icon)`  
  支持 `wheat.svg` 或 `/images/farm/icons/wheat.svg` 两种写法

### NPC 图标

- 路径：`public/images/farm/icons/npc/*.svg`
- 清单：`public/images/farm/farm-npc-icons.xlsx`
- 模型解析：`FarmTaskNpc::resolveIconUrl($icon)`

### 重新生成图标

```bash
python3 scripts/generate_farm_icons.py
python3 scripts/generate_farm_npc_icons.py
```

## 相关文档

- [核心业务开发文档.md](./核心业务开发文档.md) — 习惯/打卡/数值模块的数据表与业务设计
- [项目提醒.md](./项目提醒.md) — 备份恢复、Passport 密钥、邮件 SSL 等注意事项

## 开发约定

- API 控制器位于 `app/Api/`，后台控制器位于 `app/Admin/Controllers/`
- 后台路由：`app/Admin/routes.php`；语言包：`lang/zh_CN/`
- 新增需登录且不允许纯访客写入的接口，请加上 `check_uuid` 中间件
- 图标字段入库时建议只存文件名（如 `wheat.svg`），展示时通过 Model 的 `resolveIconUrl()` 拼接完整 URL

## License

MIT
