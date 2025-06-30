<template>
    <div class="xbcode-container">
        <div class="header-tools">
            <!-- 首页 -->
            <div class="item" @click="hanldHome()">
                <xbIcons class="icon" icon="Monitor" size="20" color="#555" />
            </div>
        </div>
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
        hanldHome() {
            window.location.href = '/'
        },
    },
}
</script>

<style lang="scss" scoped>
.xbcode-container {
    height: 100%;
    .header-tools {
        height: 100%;
        display: flex;
        justify-content: flex-end;
        align-items: center;

        .item {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            user-select: none;
            cursor: pointer;
            padding: 0 10px;

            &:hover {
                .icon {
                    -webkit-animation: logo-animation .3s ease-in-out;
                    animation: logo-animation .3s ease-in-out;
                }
            }
        }
    }
}

@keyframes logo-animation {
    0% {
        -webkit-transform: scale(0);
        transform: scale(0)
    }

    80% {
        -webkit-transform: scale(1.2);
        transform: scale(1.2)
    }

    to {
        -webkit-transform: scale(1);
        transform: scale(1)
    }
}
</style>