# 贡献指南

感谢你对 OpenIM PHP SDK 的关注与贡献。为了保持项目质量与一致性，请遵循以下规范：

## 分支与提交
- 以 `feature/`、`fix/`、`docs/` 前缀创建分支，例如：`feature/openim-client`
- 提交信息采用简洁动词前缀：`feat: add OpenIMClient`、`fix: handle token error`、`docs: update README`

## 开发流程
- 保持主分支干净：请基于最新主分支创建功能分支
- 运行测试：`composer install && vendor/bin/phpunit`
- 编写或更新相应的测试用例与文档

## Pull Request
- 描述改动目的、范围与影响面（是否有破坏性变更）
- 附上本地测试结果或截图
- 关联相关 issue（如有）

## 代码风格
- 遵循现有项目风格与约定
- 避免提交敏感信息（密钥、token）
- 对外 API 变更请更新 `README.md` 与 `CHANGELOG.md`

