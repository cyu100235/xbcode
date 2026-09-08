<template>
    <div class="xbadmin-workbench">
        <div class="xbadmin-wb-hero">
            <div class="xbadmin-wb-hero-title">{{ greeting }}，{{ displayName }}</div>
            <div class="xbadmin-wb-hero-sub">{{ web_name }} · 版本 {{ web_version }} · 当前角色 {{ roleTitle }}</div>
        </div>
        <div class="xbadmin-wb-card">
            <div class="xbadmin-wb-card-title">账号信息</div>
            <div class="xbadmin-wb-grid">
                <div class="xbadmin-wb-cell" v-for="(cell, index) in cells" :key="index">
                    <div class="xbadmin-wb-cell-label">{{ cell.label }}</div>
                    <div class="xbadmin-wb-cell-value">{{ cell.value }}</div>
                </div>
            </div>
        </div>
        <div class="xbadmin-wb-card">
            <div class="xbadmin-wb-card-title">使用指引</div>
            <ul class="xbadmin-wb-list">
                <li>权限管理 · 管理员：维护后台登录账号，并把账号挂到某个角色上。</li>
                <li>权限管理 · 角色：给角色分配可访问的菜单与接口，账号权限随角色变化即时生效。</li>
                <li>权限管理 · 菜单：维护左侧菜单树与接口权限点，按钮类型的菜单就是接口鉴权依据。</li>
                <li>超级管理员默认放行全部权限，普通管理员只能看到被授权的菜单。</li>
            </ul>
        </div>
        <div class="xbadmin-wb-footer" v-if="copyright">{{ copyright }}</div>
    </div>
</template>

<script>
export default {
    props: {
        web_name: {
            type: String,
            default: '后台管理系统',
        },
        web_version: {
            type: String,
            default: '1.0.0',
        },
        web_url: {
            type: String,
            default: '',
        },
        copyright: {
            type: String,
            default: '',
        },
        user: {
            type: Object,
            default: () => ({}),
        },
    },
    computed: {
        greeting() {
            const hour = new Date().getHours();
            if (hour < 6) {
                return '凌晨好';
            }
            if (hour < 12) {
                return '上午好';
            }
            if (hour < 14) {
                return '中午好';
            }
            if (hour < 18) {
                return '下午好';
            }
            return '晚上好';
        },
        displayName() {
            return this.user.nickname || this.user.username || '管理员';
        },
        roleTitle() {
            return this.user.role_title || '未分配';
        },
        cells() {
            return [
                { label: '登录账号', value: this.user.username || '-' },
                { label: '用户昵称', value: this.user.nickname || '-' },
                { label: '所属角色', value: this.roleTitle },
                { label: '账号状态', value: String(this.user.state) === '20' ? '启用' : '禁用' },
                { label: '登录IP', value: this.user.login_ip || '-' },
                { label: '最后登录时间', value: this.user.login_time || '-' },
            ];
        },
    },
};
</script>

<style scoped>
.xbadmin-workbench {
    box-sizing: border-box;
    height: 100%;
    padding: 16px;
    overflow: auto;
}

.xbadmin-wb-hero {
    box-sizing: border-box;
    padding: 22px 24px;
    margin-bottom: 16px;
    border-radius: 6px;
    background: linear-gradient(135deg, #4b7bec 0%, #3867d6 100%);
    color: #ffffff;
}

.xbadmin-wb-hero-title {
    font-size: 20px;
    font-weight: 600;
    line-height: 30px;
}

.xbadmin-wb-hero-sub {
    margin-top: 6px;
    font-size: 13px;
    opacity: 0.85;
}

.xbadmin-wb-card {
    box-sizing: border-box;
    padding: 18px 20px;
    margin-bottom: 16px;
    border: 1px solid #ebeef5;
    border-radius: 6px;
    background-color: #ffffff;
}

.xbadmin-wb-card-title {
    padding-left: 9px;
    margin-bottom: 14px;
    border-left: 3px solid #409eff;
    color: #303133;
    font-size: 15px;
    font-weight: 600;
    line-height: 18px;
}

.xbadmin-wb-grid {
    display: flex;
    flex-wrap: wrap;
}

.xbadmin-wb-cell {
    box-sizing: border-box;
    width: 33.33%;
    min-width: 200px;
    padding: 8px 12px 8px 0;
}

.xbadmin-wb-cell-label {
    color: #909399;
    font-size: 13px;
    line-height: 20px;
}

.xbadmin-wb-cell-value {
    color: #303133;
    font-size: 14px;
    line-height: 24px;
    word-break: break-all;
}

.xbadmin-wb-list {
    margin: 0;
    padding-left: 18px;
    color: #606266;
    font-size: 13px;
    line-height: 26px;
}

.xbadmin-wb-footer {
    padding: 6px 0 10px;
    color: #909399;
    font-size: 12px;
    text-align: center;
}
</style>
