<template>
    <div class="pages-container">
        应用卸载
    </div>
</template>

<script>
export default {
    props: {
        app_name: {
            type: String,
            default: ''
        },
        version: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
        }
    },
    mounted() {
        console.log('app_name', this.app_name);
        console.log('version', this.version);
        console.log(this.$attrs);
    },
    methods: {
    },
}
</script>

<style lang="scss">
</style>