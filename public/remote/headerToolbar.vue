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
        await this.checkCloud()
    },
    methods: {
        checkCloud() {
            this.$useRemote('remote/cloud/login', {}, {
                title: '云服务登录',
                customStyle: {
                    width: '420px',
                    height: '430px',
                },
            })
        }
    },
}
</script>

<style lang="scss"></style>