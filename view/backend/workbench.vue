<template>
    <div class="workbench-container" v-if="systemData">
        <!-- 版本信息/今日数据 -->
        <div class="version-today">
            <!-- 版本信息 -->
            <div class="version-container">
                <div class="title">版本信息</div>
                <div class="content">
                    <div class="item">
                        <div class="label">平台名称</div>
                        <div class="value">
                            {{ systemData.login_beian.system_name || '未设置' }}
                        </div>
                    </div>
                    <div class="item">
                        <div class="label">当前版本</div>
                        <div class="value">
                            {{ systemData.login_beian.system_version }}
                        </div>
                    </div>
                    <div class="item">
                        <div class="label">获取渠道</div>
                        <div class="value buttons">
                            <el-button type="primary" size="small">
                                官网
                            </el-button>
                            <el-button type="danger" size="small">
                                gitee
                            </el-button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 今日数据 -->
            <div class="today-container">
                <div class="title-container">
                    <div class="title">今日数据</div>
                    <div class="update-time">
                        本数据实时更新
                    </div>
                </div>
                <div class="content">
                    <div class="item">
                        <div class="label">销售额</div>
                        <div class="value">100</div>
                        <div class="sum">总：1000</div>
                    </div>
                    <div class="item">
                        <div class="label">成交订单</div>
                        <div class="value">12</div>
                        <div class="sum">总：1000</div>
                    </div>
                    <div class="item">
                        <div class="label">新增用户</div>
                        <div class="value">30</div>
                        <div class="sum">总：1000</div>
                    </div>
                    <div class="item">
                        <div class="label">新增访问量</div>
                        <div class="value">10</div>
                        <div class="sum">总：1000</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 核心配置/常用功能 -->
        <div class="core-common">
            <!-- 核心配置 -->
            <div class="core-container">
                <div class="title">核心配置</div>
                <div class="content">
                    <div class="item" v-for="(item, index) in coreMenus" :key="index" @click="hanldUrl(item.path)">
                        <img class="icon" :src="item.icon" v-if="item.icon" />
                        <div class="label">{{ item.title }}</div>
                    </div>
                </div>
            </div>
            <!-- 常用功能 -->
            <div class="common-container">
                <div class="title">常用功能</div>
                <div class="content">
                    <div class="item" v-for="(item, index) in commonMenus" :key="index" @click="hanldUrl(item.path)">
                        <img class="icon" :src="item.icon" v-if="item.icon" />
                        <div class="label">{{ item.title }}</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 访问量趋势图/销售额趋势图 -->
        <div class="visit-sale">
            <!-- 访问量趋势图 -->
            <div class="visit-container">
                <div class="title-container">
                    <div class="title">访问量趋势图</div>
                    <div class="desc">本数据实时更新</div>
                </div>
                <div class="content">
                    <div class="action" @click="hanldSwitchVisits">
                        <xbIcons icon="TurnOff" size="24" v-if="switchVisits" />
                        <xbIcons icon="Open" size="24" v-else="switchVisits" />
                    </div>
                    <div id="visit-cake"></div>
                </div>
            </div>
            <!-- 用户注册来源 -->
            <div class="sale-container">
                <div class="title-container">
                    <div class="title">用户注册来源图</div>
                    <div class="desc">本数据实时更新</div>
                </div>
                <div class="content">
                    <div id="register"></div>
                </div>
            </div>
        </div>
        <!-- 系统更新版本检测 -->
        <Teleport to="body">
            <div class="updated-version" v-if="updateInfo.status">
                <div class="updated-box">
                    <img src="/static/image/updated-bg.png" class="updated-bg" />
                    <div class="updated-title">
                        发现新版本更新，发布时间：{{ updateInfo.detail.create_at }}
                    </div>
                    <div class="updated-content-box">
                        <div class="version-box">
                            <div class="updated-version-title">
                                当前版本：{{ updateInfo.detail.local_version_name }}
                            </div>
                            <div class="updated-version-title next-version">
                                有新版本：{{ updateInfo.detail.new_version_name }}
                            </div>
                        </div>
                        <pre class="updated-content">{{ updateInfo.detail.content }}</pre>
                    </div>
                    <div class="updated-buttons">
                        <button class="action-button to-btn" @click="hanldUrl('/backend/Server/update')">
                            立即去更新
                        </button>
                        <button class="action-button cancel-btn" @click="ignoreUpdate">
                            忽略本次更新
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
        <!-- 插件更新版本检测 -->
        <Teleport to="body">
            <div class="updated-version" v-if="pluginUpdate.status">
                <div class="updated-box">
                    <img src="/static/image/updated-bg.png" class="updated-bg" />
                    <div class="updated-title">
                        发现有新的插件版本更新
                    </div>
                    <div class="updated-content-box">
                        <div class="version-box">
                            <div class="updated-version-title">
                                温馨提示
                            </div>
                            <div class="updated-version-title next-version">
                                以下列表均为有更新的插件
                            </div>
                        </div>
                        <pre class="updated-content" v-html="pluginUpdate.detail.content"></pre>
                    </div>
                    <div class="updated-buttons">
                        <button class="action-button to-btn" @click="hanldUrl('/backend/Plugins/index')">
                            立即去更新
                        </button>
                        <button class="action-button cancel-btn" @click="ignorePluginUpdate">
                            忽略本次更新
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script>
let visitEcharts = null
let saleEcharts = null
export default {
    data() {
        return {
            systemData: null,
            // 系统版本更新信息
            updateInfo: {
                status: false,
                detail: {
                    // 发布时间
                    create_at: '',
                    // 当前版本
                    version: '',
                    // 新版本
                    new_version: '',
                    // 更新内容
                    content: '',
                },
            },
            // 插件版本更新
            pluginUpdate: {
                status: false,
                detail: {
                    // 当前版本KEY
                    version_key:'',
                    // 更新内容
                    content: '',
                },
            },
            coreMenus: [
                {
                    title: '网站配置',
                    icon: '/static/image/workbench/web.png',
                    path: '/backend/Setting/config/backend/system?redirect=/workbench',
                },
                {
                    title: '版权设置',
                    icon: '/static/image/workbench/protocol.png',
                    path: '/backend/Setting/config/backend/copyright?redirect=/workbench',
                },
                {
                    title: '上传设置',
                    icon: '/static/image/workbench/upload.png',
                    path: '/backend/UploadConf/index?redirect=/workbench',
                },
                {
                    title: '定时任务',
                    icon: '/static/image/workbench/pay.png',
                    path: '/backend/Crontab/index?redirect=/workbench',
                },
                {
                    title: '账号管理',
                    icon: '/static/image/workbench/auth.png',
                    path: '/backend/Admin/index?redirect=/workbench',
                },
                {
                    title: '部门管理',
                    icon: '/static/image/workbench/role.png',
                    path: '/backend/AdminRole/index?redirect=/workbench',
                },
            ],
            commonMenus: [
                {
                    title: '系统日志',
                    icon: '/static/image/workbench/log.png',
                    path: '/backend/AdminLog/index?redirect=/workbench',
                },
                {
                    title: '插件管理',
                    icon: '/static/image/workbench/log.png',
                    path: '/backend/Plugins/index?redirect=/workbench',
                },
                {
                    title: '站点管理',
                    icon: '/static/image/workbench/hot.png',
                    path: '/backend/WebSite/index?redirect=/workbench',
                },
                {
                    title: '站点公告',
                    icon: '/static/image/workbench/hot.png',
                    path: '/backend/WebNotice/index?redirect=/workbench',
                },
            ],
            // 是否切换访问量趋势图 true: 柱状图 false: 折线图
            switchVisits: false,
        }
    },
    mounted() {
        // 获取系统信息
        this.systemData = this.$siteApp.siteInfo
        this.$nextTick(() => {
            setTimeout(() => {
                // 获取系统版本更新信息
                this.getVersionInfo();
                // 获取插件版本更新信息
                this.getPluginVersion();
                // 初始化访问量趋势图（折线图）
                this.initLineChart()
                // 初始化用户注册来源图
                this.registerEcharts()
            }, 500)
        })
    },
    methods: {
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
                    data: ['11月01日','11月02日', '11月03日', '11月04日', '11月05日', '11月06日', '11月07日']
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
                            color: new this.$echarts.graphic.LinearGradient(
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
                            color: new this.$echarts.graphic.LinearGradient(
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
                            color: new this.$echarts.graphic.LinearGradient(
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
            visitEcharts = this.$echarts.init(chartDom, null, {
                renderer: 'svg'
            });
            visitEcharts.setOption(option)
        },
        // 初始化用户注册来源图
        registerEcharts() {
            var series = [
                {
                    name: 'Access From',
                    type: 'pie',
                    radius: '50%',
                    data: [
                        { value: 1048, name: '电脑端' },
                        { value: 735, name: '移动端' },
                        { value: 580, name: '微信小程序' },
                        { value: 484, name: '微信公众号' },
                        { value: 300, name: 'APP' }
                    ],
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
                    subtext: '以下数据仅供参考',
                    left: 'center'
                },
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    orient: 'vertical',
                    left: 'left'
                },
                series: series
            };
            var chartDom = document.getElementById('register');
            saleEcharts = this.$echarts.init(chartDom, null, {
                renderer: 'svg'
            });
            saleEcharts.setOption(option)
        },
        // 跳转页面
        hanldUrl(path) {
            this.$router.push(path)
        },
        // 忽略版本更新
        ignoreUpdate() {
            const ignoreVersionName = this.updateInfo.detail.new_version_name;
            localStorage.setItem("ignore_system_version", ignoreVersionName);
            window.location.reload();
        },
        // 忽略插件版本更新
        ignorePluginUpdate() {
            const ignoreVersionName = this.pluginUpdate.detail.version_key;
            localStorage.setItem("ignore_plugin_version", ignoreVersionName);
            window.location.reload();
        },
        // 获取系统版本信息
        getVersionInfo() {
            const ignoreVersionName = localStorage.getItem("ignore_system_version");
            this.$useGet('backend/Server/version').then(res => {
                const result = res.data ?? this.updateInfo
                if (ignoreVersionName) {
                    // 存在忽略版本，且忽略版本不等于新版本
                    if (ignoreVersionName !== result?.detail?.new_version_name) {
                        this.updateInfo = result
                    }
                } else {
                    this.updateInfo = result
                }
            })
        },
        // 获取插件版本信息
        getPluginVersion() {
            const ignoreVersionName = localStorage.getItem("ignore_plugin_version");
            this.$useGet('backend/Server/plugins').then(res => {
                const result = res.data ?? this.pluginUpdate
                if (ignoreVersionName) {
                    // 存在忽略版本，且忽略版本不等于新版本
                    if (ignoreVersionName !== result?.detail?.version_key) {
                        this.pluginUpdate = result
                    }
                } else {
                    this.pluginUpdate = result
                }
            })
        },
    },
}
</script>

<style lang="scss">
.workbench-container {
    height: 100%;
    padding: 15px;
    display: flex;
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

            .title {
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

                    // .label {}

                    .value {
                        flex: 1;
                        display: flex;
                        padding-left: 20px;
                    }
                    .buttons{
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

                .title {
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

                    // .label {}

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
        margin-top: 15px;
        display: flex;
        gap: 15px;

        .core-container {
            flex: 1;
            background-color: #fff;
            font-size: 14px;
            border-radius: 6px;

            .title {
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

                    .label {
                        font-size: 12px;
                    }
                }
            }
        }

        .common-container {
            flex: 1;
            background-color: #fff;
            font-size: 14px;
            border-radius: 6px;

            .title {
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

                    .label {
                        font-size: 12px;
                        margin-top: 6px;
                    }
                }
            }
        }
    }

    .visit-sale {
        flex: 1;
        display: flex;
        gap: 15px;
        margin-top: 15px;

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

                .title {
                    font-size: 14px;
                }

                .desc {
                    font-size: 12px;
                    color: #999;
                }
            }

            .content {
                height: 100%;
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

                .title {
                    font-size: 14px;
                }

                .desc {
                    font-size: 12px;
                    color: #999;
                }
            }

            .content {
                height: 100%;
                padding: 20px;
                overflow: hidden;

                #register {
                    height: 100%;
                }
            }
        }
    }
}
.updated-version {
    position: fixed;
    inset: 0;
    background: rgba(#000, .4);
    -webkit-backdrop-filter: blur(6px);
    backdrop-filter: blur(6px);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;

    .updated-box {
        width: 400px;
        display: flex;
        flex-direction: column;
        background: #fff;
        box-shadow: 2px 2px 10px rgba(#000, .1);

        .updated-bg {
            height: 100px;
            background: #722ED1;
            object-fit: cover;
        }

        .updated-title {
            padding: 10px 15px;
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #333;
            font-weight: 700;
        }

        .updated-content-box {
            border-top: 1px solid #e5e5e5;
            border-bottom: 1px solid #e5e5e5;
            padding: 0 15px;
            height: 280px;
            overflow-y: auto;
            overflow-x: hidden;

            .version-box {
                display: flex;
                gap: 10px;
                color: #333;
                font-size: 12px;
                padding: 5px 0;

                // .updated-version-title {}

                .next-version {
                    color: #ff5900;
                }
            }

            .updated-content {
                line-height: 25px;
                font-size: 12px;
                color: #666;
                display: block;
                width: 100%;
                white-space: pre-wrap;
                word-break: break-word;
            }
        }

        .updated-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 0;
            gap: 20px;

            .action-button {
                color: #fff;
                padding: 8px 15px;
                border-radius: 4px;
                font-size: 12px;
                cursor: pointer;
                border: none;
            }

            .to-btn {
                background: #722ED1;

                &:hover {
                    background: #a065e9;
                }
            }

            .cancel-btn {
                background: #FF7D00;

                &:hover {
                    background: #ff9d00;
                }
            }
        }
    }
}
</style>