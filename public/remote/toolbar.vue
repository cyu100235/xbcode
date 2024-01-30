<template>
    <div class="pages-container">
    </div>
</template>

<script>
export default {
    data() {
        return {
        }
    },
    async mounted() {
        // 检测是否登录
        await this.checkLogin()
    },
    methods: {
        checkLogin() {
            this.$http.useGet('admin/Cloud/user').then((res) => {
                if (res?.data?.code === 12000) {
                    this.checkCloud()
                }
            })
        },
        checkCloud() {
            this.$useRemote('remote/cloud/login', {}, {
                title: '云服务登录',
                showClose:false,
                customStyle: {
                    width: '420px',
                    height: '350px',
                },
            })
        }
    },
}
</script>

<style lang="scss"></style>