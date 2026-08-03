<script>
import('/app/xbCode/static/js/marked.min.js')
import('/app/xbCode/static/js/prism-1.30.0/themes/prism.css')
import('/app/xbCode/static/js/prism-1.30.0/prism.js')
import('/app/xbCode/static/js/prism-1.30.0/components/prism-core.js')
import('/app/xbCode/static/js/prism-1.30.0/plugins/autoloader/prism-autoloader.js')

export default {
    props: {
        readme: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            showImagePreview: false,
            previewImageSrc: ''
        }
    },
    mounted() {
        this.renderReadme()
    },
    methods: {
        renderReadme() {
            setTimeout(() => {
                // 获取页面元素
                const content = document.getElementById('content')
                // 配置 marked 渲染器,让链接在新窗口打开
                const renderer = new marked.Renderer();
                const linkRenderer = renderer.link
                renderer.link = function (href, title, text) {
                    const html = linkRenderer.call(renderer, href, title, text)
                    return html.replace(/^<a /, '<a target="_blank" ')
                };
                marked.setOptions({ renderer: renderer });
                content.innerHTML = marked.parse(this.readme);

                // 为所有图片添加点击事件
                this.$nextTick(() => {
                    const images = content.querySelectorAll('img')
                    images.forEach(img => {
                        img.style.cursor = 'pointer'
                        img.addEventListener('click', (e) => {
                            this.previewImage(e.target.src)
                        })
                    })
                })
            }, 500)
        },
        previewImage(src) {
            this.previewImageSrc = src
            this.showImagePreview = true
        },
        closePreview() {
            this.showImagePreview = false
            this.previewImageSrc = ''
        }
    },
}
</script>

<template>
    <div class="readme-container">
        <div id="content"></div>
        <!-- 图片预览遮罩层 -->
        <div v-if="showImagePreview" class="image-preview-mask" @click="closePreview">
            <div class="preview-container">
                <img :src="previewImageSrc" class="preview-image" @click.stop>
                <div class="close-btn" @click="closePreview">✕</div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.readme-container {
    height: 100%;
    overflow: hidden;

    #content {
        height: 100%;
        padding: 20px;
        overflow-y: auto;
        overflow-x: hidden;
        line-height: 1.8rem;
        font-size: 14px;

        ul,
        ol {
            list-style: auto;
            padding: 0 20px;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p {
            padding: 4px 0;
        }

        pre {
            background: #282c34;
            color: #abb2bf;
            padding: 20px;
            border-radius: 6px;
            overflow-x: auto;
            margin: 16px 0;

            code {
                background: transparent;
                padding: 0;
                color: inherit;
                text-shadow: none;

                .operator {
                    background: transparent;
                }
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;

            thead {
                tr {
                    th {
                        background-color: #f1f1f1;
                    }
                }
            }
        }

        th {
            border: 1px solid #ccc;
            min-width: 50px;
            height: 20px;
            padding: 5px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        blockquote {
            border-left: 8px solid #B4D5FF;
            padding: 10px 10px;
            margin: 10px 0;
            background-color: #f1f1f1;
        }

        a {
            color: #42b983;
            text-decoration: none;
            transition: all 0.3s;

            &:hover {
                color: #00bb67;
                text-decoration: underline;
            }
        }

        img {
            width: 100%;
        }
    }
}

// 图片预览样式
.image-preview-mask {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.85);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.2s ease;

    .preview-container {
        position: relative;
        max-width: 90%;
        max-height: 90%;
        display: flex;
        align-items: center;
        justify-content: center;

        .preview-image {
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 4px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            animation: zoomIn 0.3s ease;
        }

        .close-btn {
            position: absolute;
            top: -40px;
            right: 0;
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            transition: all 0.3s;

            &:hover {
                background: #fff;
                transform: rotate(90deg);
            }
        }
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

@keyframes zoomIn {
    from {
        transform: scale(0.8);
        opacity: 0;
    }

    to {
        transform: scale(1);
        opacity: 1;
    }
}
</style>
