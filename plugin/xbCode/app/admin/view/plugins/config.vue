<template>
    <div class="xb-config">
        <xbBuilder :schema="schema" />
    </div>
</template>

<script>
export default {
    props: {
        name: String,
        schema: Object
    },
    data() {
        return {
        }
    },
    mounted() {        
    },
    methods: {
    },
}
</script>

<style lang="scss" scoped>
.xb-config {
    height: 100%;

    .empty {
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
}
</style>