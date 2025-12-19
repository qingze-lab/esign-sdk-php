# 变更日志

## v0.3.0
- 新增 `OpenIMClient` 简化初始化流程（仅暴露服务：users/messages/groups/conversations）
- 新增 `RedisCache`（PSR-16 实现），并提供使用示例与测试
- 统一集成测试使用 `OpenIMClient`
- `UserService` 增加便捷方法：`register`、`registerMany`、`updateEx`、`getUsersInfoList`
- `GroupService` 增加便捷方法：`createSimple`、`setGroupName`、`invite`、`kick`、`dismiss`、`transferTo`
- 更新 `README.md` 为简化用法，新增 Redis 缓存示例

