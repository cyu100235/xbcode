<template>
    <div class="pages-container" v-if="detail">
        插件详情
    </div>
</template>

<script>
export default {
    props: {
        app_name: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            detail: null,
        }
    },
    async mounted() {
        await this.getDetail()
    },
    methods: {
        // 获取详情
        getDetail() {
            const params = {
                app_name: this.app_name
            }
            const url = `${this.$moduleName}/Plugins/detail`
            this.$http.useGet(url, params).then(res => {
                this.detail = res?.data ?? null
            })
        }
    },
}
</script>

<style lang="scss" scoped>
</style>