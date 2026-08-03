<template>
    <div>
        <ElButton type="primary" @click="hanldClose">
            上传附件
        </ElButton>
    </div>
</template>

<script>
    export default {
        data() {
            return {
            }
        },
        mounted() {
        },
        methods: {
            hanldClose(){
                this.$emit('close')
            },
        }
    }
</script>

<style lang="scss" scoped>
</style>