<template>
    <div class="upload-container">
        <ElUpload class="upload-wrap" :action="action" drag multiple>
            <XbIcons icon="UploadFilled" size="28" />
            <div class="el-upload__text">
                将文件拖放到此处或 <em>点击上传</em>
            </div>
        </ElUpload>
    </div>
</template>

<script>
export default {
    data() {
        return {
            action: '',
        }
    },
    mounted() {
    },
    methods: {
        handlePreview(file) {
            this.$refs.uploadDemo.preview(file);
        },
        handleRemove(file, fileList) {
            this.$refs.uploadDemo.remove(file);
        },
        handleSuccess(res, file, fileList) {
            console.log(res, file, fileList);
        },
        hanldClose() {
            this.$emit('close')
        },
    }
}
</script>

<style lang="scss" scoped>
.upload-container {
    height: 100%;
    padding: 15px;
}
</style>