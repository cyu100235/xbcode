<template>
    <div class="workbench-container">
        <!-- 版本信息/今日数据 -->
        <div class="version-today">
            <!-- 版本信息 -->
            <div class="version-container">
                <div class="header-title">版本信息</div>
                <div class="content">
                    <div class="item">
                        <div class="xb-title">平台名称</div>
                        <div class="value">
                            {{ systemData?.web_name || 'XBCode' }}
                        </div>
                    </div>
                    <div class="item">
                        <div class="xb-title">当前版本</div>
                        <div class="value">
                            {{ systemData?.web_version || '1.0.0' }}
                        </div>
                    </div>
                    <div class="item">
                        <div class="xb-title">获取渠道</div>
                        <div class="value buttons">
                            <el-button type="primary" size="small" @click="hanldUrl('http://xbcode.net')">
                                官方网站
                            </el-button>
                            <el-button type="warning" size="small" @click="hanldUrl('http://xbcode.net')">
                                使用文档
                            </el-button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 今日数据 -->
            <div class="today-container">
                <div class="title-container">
                    <div class="xb-title">今日数据</div>
                    <div class="update-time">
                        本数据实时更新
                    </div>
                </div>
                <div class="content">
                    <div class="item">
                        <div class="xb-title">销售额</div>
                        <div class="value">0</div>
                        <div class="sum">总：1000</div>
                    </div>
                    <div class="item">
                        <div class="xb-title">成交订单</div>
                        <div class="value">0</div>
                        <div class="sum">总：1000</div>
                    </div>
                    <div class="item">
                        <div class="xb-title">新增用户</div>
                        <div class="value">0</div>
                        <div class="sum">总：1000</div>
                    </div>
                    <div class="item">
                        <div class="xb-title">新增访问量</div>
                        <div class="value">0</div>
                        <div class="sum">总：1000</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 核心配置/常用功能 -->
        <div class="core-common">
            <!-- 核心配置 -->
            <div class="core-container">
                <div class="header-title">核心配置</div>
                <div class="content">
                    <div class="item" v-for="(item, index) in coreMenus" :key="index" @click="hanldUrl(item.path)">
                        <img class="icon" :src="item.icon" v-if="item.icon" />
                        <div class="xb-title">{{ item.title }}</div>
                    </div>
                </div>
            </div>
            <!-- 常用功能 -->
            <div class="common-container">
                <div class="header-title">常用功能</div>
                <div class="content">
                    <div class="item" v-for="(item, index) in commonMenus" :key="index" @click="hanldUrl(item.path)">
                        <img class="icon" :src="item.icon" v-if="item.icon" />
                        <div class="xb-title">{{ item.title }}</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 访问量趋势图/销售额趋势图 -->
        <div class="visit-sale">
            <!-- 访问量趋势图 -->
            <div class="visit-container">
                <div class="title-container">
                    <div class="xb-title">访问量趋势图</div>
                    <div class="desc">本数据实时更新</div>
                </div>
                <div class="content">
                    <div class="action" @click="hanldSwitchVisits">
                        <XbIcons icon="TurnOff" size="24" v-if="switchVisits" />
                        <XbIcons icon="Open" size="24" v-else="switchVisits" />
                    </div>
                    <div id="visit-cake"></div>
                </div>
            </div>
            <!-- 用户注册来源 -->
            <div class="sale-container">
                <div class="title-container">
                    <div class="xb-title">用户注册来源图</div>
                    <div class="desc">本数据实时更新</div>
                </div>
                <div class="content">
                    <div id="register"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
let visitEcharts = null
let saleEcharts = null
export default {
    props: {
        componentId: String
    },
    data() {
        return {
            systemData: null,
            coreMenus: [
                {
                    title: '网站配置',
                    icon: '/app/xbCode/static/image/workbench/web.png',
                    path: '/app/xbCode/admin/Setting/system',
                },
                {
                    title: '上传设置',
                    icon: '/app/xbCode/static/image/workbench/upload.png',
                    path: '/app/xbUpload/admin/Engine/index',
                },
                {
                    title: '账号管理',
                    icon: '/app/xbCode/static/image/workbench/auth.png',
                    path: '/app/xbCode/admin/Admin/index',
                },
                {
                    title: '角色管理',
                    icon: '/app/xbCode/static/image/workbench/role.png',
                    path: '/app/xbCode/admin/AdminRole/index',
                },
            ],
            commonMenus: [
                {
                    title: '插件管理',
                    icon: '/app/xbCode/static/image/workbench/log.png',
                    path: '/app/xbCode/admin/Plugins/index',
                },
            ],
            // 是否切换访问量趋势图 true: 柱状图 false: 折线图
            switchVisits: false,
            // 是否移动端窄屏（<768px），用于图表适配
            isNarrowScreen: false,
        }
    },
    mounted() {      
        this.systemData = this.$xbcode.siteApp.siteInfo
        this.isNarrowScreen = window.innerWidth <= 767
        this.$nextTick(() => {
            setTimeout(() => {
                // 初始化访问量趋势图（折线图）
                this.initLineChart()
                // 初始化用户注册来源图
                this.registerEcharts()
            }, 500)
        })
        // 监听窗口尺寸变化，重绘图表（适配移动端/平板端）
        window.addEventListener('resize', this.handleResize)
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.handleResize)
    },
    methods: {
        // 窗口尺寸变化时重绘图表
        handleResize() {
            if (visitEcharts) visitEcharts.resize()
            if (saleEcharts) saleEcharts.resize()
            // 跨越移动端断点时重新应用注册来源图配置
            const isNarrow = window.innerWidth <= 767
            if (isNarrow !== this.isNarrowScreen) {
                this.isNarrowScreen = isNarrow
                if (saleEcharts) saleEcharts.setOption(this.getRegisterOption(), true)
            }
        },
        // 切换访问量趋势图
        hanldSwitchVisits() {
            this.switchVisits = !this.switchVisits
            if (this.switchVisits) {
                // 切换柱状图
                this.columnar()
            } else {
                // 切换折线图
                this.lineChart()
            }
        },
        // 获取访问量趋势图数据
        getVisitData() {
            return {
                xAxis: {
                    type: 'category',
                    data: ['11月01日', '11月02日', '11月03日', '11月04日', '11月05日', '11月06日', '11月07日']
                },
                yAxis: {
                    type: 'value'
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        label: {
                            backgroundColor: '#4B5EFF'
                        }
                    }
                },
                series: [
                    {
                        // 设置数据
                        data: [180, 150, 350, 200, 320, 280, 350],
                        // 设置线条
                        type: 'line',
                        // 设置线条平滑
                        smooth: true,
                        // 设置区域样式
                        areaStyle: {
                            // 设置渐变色
                            color: new this.$xbcode.echarts.graphic.LinearGradient(
                                // 渐变方向：从上到下
                                0, 0, 0, 1,
                                [
                                    { offset: 0, color: '#c9cef7' },
                                    { offset: 1, color: '#4B5EFF' }
                                ]
                            )
                        },
                        // 设置柱状样式
                        itemStyle: {
                            // 设置左上、右上、右下、左下的圆角
                            borderRadius: [30, 30, 0, 0],
                            // 设置柱状颜色
                            // color: '#016eff',
                            // 设置柱状渐变色
                            color: new this.$xbcode.echarts.graphic.LinearGradient(
                                // 渐变方向：从上到下
                                0, 0, 0, 1,
                                [
                                    { offset: 0, color: '#7383F7' },
                                    { offset: 1, color: '#4B5EFF' }
                                ]
                            )
                        }
                    }
                ]
            };
        },
        // 切换访问量趋势图（柱状）
        columnar() {
            const option = {
                xAxis: {
                    type: 'category',
                    data: [
                        '11月01日',
                        '11月02日',
                        '11月03日',
                        '11月04日',
                        '11月05日',
                        '11月06日',
                        '11月07日'
                    ]
                },
                yAxis: {
                    type: 'value'
                },
                series: [
                    {
                        // 设置数据
                        data: [120, 200, 150, 80, 70, 110, 130],
                        // 设置柱状图
                        type: 'bar',
                        // 设置柱状宽度
                        barWidth: 25,
                        // 设置柱状样式
                        itemStyle: {
                            // 设置左上、右上、右下、左下的圆角
                            borderRadius: [30, 30, 0, 0],
                            // 设置柱状颜色
                            // color: '#016eff',
                            // 设置柱状渐变色
                            color: new this.$xbcode.echarts.graphic.LinearGradient(
                                // 渐变方向：从上到下
                                0, 0, 0, 1,
                                [
                                    { offset: 0, color: '#7383F7' },
                                    { offset: 1, color: '#4B5EFF' }
                                ]
                            )
                        }
                    }
                ]
            };
            visitEcharts.setOption(option)
        },
        // 切换访问量趋势图（折线图）
        lineChart() {
            const option = this.getVisitData()
            visitEcharts.setOption(option)
        },
        // 初始化折线图
        initLineChart() {
            const option = this.getVisitData()
            var chartDom = document.getElementById('visit-cake');
            visitEcharts = this.$xbcode.echarts.init(chartDom, null, {
                renderer: 'svg'
            });
            visitEcharts.setOption(option)
        },
        // 获取用户注册来源图配置（移动端窄屏：隐藏图内标题与外置标签，图例移到底部横向排列，缩小饼图）
        getRegisterOption() {
            const isNarrow = window.innerWidth <= 767
            var series = [
                {
                    name: 'Access From',
                    type: 'pie',
                    radius: isNarrow ? ['28%', '40%'] : '50%',
                    center: isNarrow ? ['50%', '46%'] : ['50%', '50%'],
                    data: [
                        { value: 1048, name: '电脑端' },
                        { value: 735, name: '移动端' },
                        { value: 580, name: '微信小程序' },
                        { value: 484, name: '微信公众号' },
                        { value: 300, name: 'APP' }
                    ],
                    // 窄屏隐藏外置标签与引导线，避免文字在扇形上重叠，改由图例+tooltip 展示
                    label: {
                        show: !isNarrow
                    },
                    labelLine: {
                        show: !isNarrow
                    },
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ];
            const option = {
                title: {
                    text: '用户注册来源',
                    subtext: isNarrow ? '' : '以下数据仅供参考',
                    show: !isNarrow,
                    left: 'center',
                    textStyle: {
                        fontSize: isNarrow ? 13 : 18
                    }
                },
                tooltip: {
                    trigger: 'item'
                },
                legend: isNarrow
                    ? {
                        orient: 'horizontal',
                        left: 'center',
                        bottom: 4,
                        itemWidth: 10,
                        itemHeight: 10,
                        itemGap: 6,
                        textStyle: {
                            fontSize: 11
                        }
                    }
                    : {
                        orient: 'vertical',
                        left: 'left'
                    },
                series: series
            };
            return option
        },
        // 初始化用户注册来源图
        registerEcharts() {
            const option = this.getRegisterOption()
            var chartDom = document.getElementById('register');
            saleEcharts = this.$xbcode.echarts.init(chartDom, null, {
                renderer: 'svg'
            });
            saleEcharts.setOption(option)
        },
        // 跳转页面
        hanldUrl(url) {
            // 检测是否链接
            if (url.includes('http://') || url.includes('https://')) {
                window.open(url)
                return
            }
            this.$router.push({
                path: url,
                query: {
                    redirect: this.$route.fullPath
                },
            })
        },
    },
}
</script>

<style lang="scss" scoped>
.workbench-container {
    height: 100%;
    display: flex;
    gap:15px;
    flex-direction: column;

    .version-today {
        display: flex;
        justify-content: space-between;
        gap: 15px;

        .version-container {
            width: 350px;
            background-color: #fff;
            font-size: 14px;
            border-radius: 6px;

            .header-title {
                user-select: none;
                padding: 15px;
                border-bottom: 1px solid #f0f0f0;
            }

            .content {
                padding: 15px;
                display: flex;
                flex-direction: column;

                .item {
                    display: flex;
                    justify-content: space-between;
                    padding: 5px 0;

                    .xb-title {
                        display: flex;
                        align-items: center;
                        color: #333;
                    }

                    .value {
                        flex: 1;
                        display: flex;
                        padding-left: 20px;
                    }

                    .buttons {
                        gap: 15px;
                    }
                }
            }
        }

        .today-container {
            flex: 1;
            background-color: #fff;
            border-radius: 6px;
            display: flex;
            flex-direction: column;

            .title-container {
                padding: 15px;
                display: flex;
                justify-content: space-between;
                border-bottom: 1px solid #f0f0f0;
                font-size: 14px;

                .xb-title {
                    user-select: none;
                }

                .update-time {
                    float: right;
                    font-size: 12px;
                    color: #999;
                }
            }

            .content {
                flex: 1;
                display: grid;
                grid-template-columns: repeat(4, 1fr);

                .item {
                    padding: 15px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    gap: 10px;

                    // .xb-title {}

                    .value {
                        font-size: 20px;
                    }

                    .sum {
                        font-size: 12px;
                        color: #999;
                    }
                }
            }
        }
    }

    .core-common {
        display: flex;
        gap: 15px;

        .core-container {
            flex: 1;
            background-color: #fff;
            font-size: 14px;
            border-radius: 6px;

            .header-title {
                user-select: none;
                padding: 15px;
                border-bottom: 1px solid #f0f0f0;
            }

            .content {
                padding: 15px;
                display: grid;
                grid-template-columns: repeat(6, 1fr);
                gap: 15px;

                .item {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 15px 0;
                    cursor: pointer;

                    &:hover {
                        color: var(--el-color-primary);
                    }

                    .icon {
                        width: 40px;
                        height: 40px;
                        border-radius: 6px;
                    }

                    .xb-title {
                        font-size: 12px;
                        color: #333;
                    }
                }
            }
        }

        .common-container {
            flex: 1;
            background-color: #fff;
            font-size: 14px;
            border-radius: 6px;

            .header-title {
                user-select: none;
                padding: 15px;
                border-bottom: 1px solid #f0f0f0;
            }

            .content {
                padding: 15px;
                display: grid;
                grid-template-columns: repeat(6, 1fr);
                gap: 15px;

                .item {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 15px 0;
                    cursor: pointer;

                    &:hover {
                        color: var(--el-color-primary);
                    }

                    .icon {
                        width: 40px;
                        height: 40px;
                        border-radius: 6px;
                    }

                    .xb-title {
                        font-size: 12px;
                        margin-top: 6px;
                        font-weight: normal;
                    }
                }
            }
        }
    }

    .visit-sale {
        flex: 1;
        display: flex;
        gap: 15px;

        .visit-container {
            flex: 1;
            height: 100%;
            background-color: #fff;
            display: flex;
            flex-direction: column;

            .title-container {
                display: flex;
                justify-content: space-between;
                border-bottom: 1px solid #f0f0f0;
                padding: 15px;
                user-select: none;

                .xb-title {
                    font-size: 14px;
                }

                .desc {
                    font-size: 12px;
                    color: #999;
                }
            }

            .content {
                flex: 1;
                min-height: 0;
                overflow: hidden;
                position: relative;

                .action {
                    position: absolute;
                    top: 6px;
                    right: 15px;
                    color: #666;
                    cursor: pointer;
                    z-index: 999;
                }

                #visit-pillar {
                    height: 100%;
                }

                #visit-cake {
                    height: 100%;
                }
            }
        }

        .sale-container {
            flex: 1;
            height: 100%;
            background-color: #fff;
            display: flex;
            flex-direction: column;

            .title-container {
                display: flex;
                justify-content: space-between;
                border-bottom: 1px solid #f0f0f0;
                padding: 15px;
                user-select: none;

                .xb-title {
                    font-size: 14px;
                }

                .desc {
                    font-size: 12px;
                    color: #999;
                }
            }

            .content {
                flex: 1;
                min-height: 0;
                padding: 20px;
                overflow: hidden;

                #register {
                    height: 100%;
                }
            }
        }
    }

    // 各卡片标题不换行
    .version-container .content .item .xb-title,
    .today-container .content .item .xb-title,
    .core-container .content .item .xb-title,
    .common-container .content .item .xb-title {
        white-space: nowrap;
    }

    // ==================== 响应式适配 ====================

    // 平板横屏 / 小桌面：992px - 1199px
    @media screen and (max-width: 1199px) {
        .version-today {
            .version-container {
                width: 280px;
            }
        }

        .core-common {
            .core-container,
            .common-container {
                .content {
                    grid-template-columns: repeat(4, 1fr);
                }
            }
        }
    }

    // 平板竖屏：768px - 991px
    @media screen and (max-width: 991px) {
        height: 100%;
        overflow-x: hidden;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;

        .version-today,
        .core-common,
        .visit-sale {
            flex: none;
        }

        .version-today {
            flex-direction: column;

            .version-container {
                width: 100%;
            }

            .today-container {
                .content {
                    grid-template-columns: repeat(2, 1fr);
                }
            }
        }

        .core-common {
            flex-direction: column;

            .core-container,
            .common-container {
                .content {
                    grid-template-columns: repeat(4, 1fr);
                }
            }
        }

        .visit-sale {
            flex-direction: column;

            .visit-container,
            .sale-container {
                flex: none;
                height: 320px;
            }
        }
    }

    // 移动端：< 768px
    @media screen and (max-width: 767px) {
        height: 100%;
        overflow-x: hidden;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;

        .version-today,
        .core-common,
        .visit-sale {
            flex: none;
        }

        .version-today {
            flex-direction: column;

            .version-container {
                width: 100%;
            }

            .today-container {
                .content {
                    grid-template-columns: repeat(2, 1fr);

                    .item {
                        padding: 12px 8px;
                        gap: 6px;

                        .value {
                            font-size: 18px;
                        }
                    }
                }
            }
        }

        .core-common {
            flex-direction: column;

            .core-container,
            .common-container {
                .content {
                    grid-template-columns: repeat(3, 1fr);
                    gap: 10px;
                }
            }
        }

        .visit-sale {
            flex-direction: column;

            .visit-container {
                flex: none;
                height: 260px;
            }

            .sale-container {
                flex: none;
                height: 340px;

                .content {
                    padding: 10px;
                }
            }
        }
    }
}
</style>