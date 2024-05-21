<template>
    <div class="pages-container">
        云服务中心
    </div>
</template>

<script>
export default {
    data() {
        return {
        }
    },
    mounted() {
        this.getUser()
    },
    methods: {
        async getUser() {
            const data = await this.$http.useGet('admin/Cloud/index').catch((err) => {
                return err
            })
            if (data?.code === 11000) {
                this.$emit("update:closeWin");
                this.$useRemote('/vue/admin/cloud/login', {}, {
                    title: '云服务登录',
                    customStyle: {
                        width: '420px',
                        height: '450px',
                    },
                })
                return;
            }
        }
    },
}
</script>

<style lang="scss" scoped>
.pages-container {
    height: 100%;
}
</style>