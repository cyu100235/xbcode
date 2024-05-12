<template>
    <div class="pages-container">
        订单
    </div>
</template>

<script>
export default {
    data() {
        return {
            detail:null
        }
    },
    mounted() {
        this.getData()
    },
    methods: {
        async getData() {
            const params = {
                name: this.$attrs?.name,
                version: this.$attrs?.version
            }
            const url = 'admin/Plugins/detail'
            const data = await this.$http.useGet(url, params).catch((err) => {
                return err
            })
            if (data?.code !== 200) {
                this.$emit("update:closeWin");
                return
            }
            this.detail = data?.data
        }
    },
}
</script>

<style lang="scss" scoped>
.pages-container {
    height: 100%;
}
</style>