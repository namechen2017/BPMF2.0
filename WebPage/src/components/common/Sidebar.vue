<template>
    <div class="sidebar">
        <el-menu :default-active="onRoutes" class="el-menu-vertical-demo" theme="dark" unique-opened router>
            <el-menu-item index="readme">
                <i class="el-icon-setting"></i>首页
            </el-menu-item>
            <el-submenu index="2">
                <template slot="title"><i class="el-icon-menu"></i>表格</template>
                <el-menu-item index="basetable">基础表格</el-menu-item>
                <el-menu-item index="vuetable">Vue表格组件</el-menu-item>
            </el-submenu>
            <el-submenu index="3">
                <template slot="title"><i class="el-icon-date"></i>表单</template>
                <el-menu-item index="baseform">基本表单</el-menu-item>
                <el-menu-item index="vueeditor">编辑器</el-menu-item>
                <el-menu-item index="markdown">markdown</el-menu-item>
                <el-menu-item index="upload">文件上传</el-menu-item>
            </el-submenu>
            <el-submenu index="4">
                <template slot="title"><i class="el-icon-star-on"></i>图表</template>
                <el-menu-item index="basecharts">基础图表</el-menu-item>
                <el-menu-item index="mixcharts">混合图表</el-menu-item>
            </el-submenu>
            <el-submenu index="5">
                <template slot="title"><i class="el-icon-star-on"></i>编号</template>
                <el-menu-item index="rules">编号</el-menu-item>
                <el-menu-item index="ruleslist">列表</el-menu-item>
            </el-submenu>
            <el-submenu index="6">
                <template slot="title" ><i class="el-icon-date"></i>工单</template>
                <el-menu-item index="testDesk">工单任务台</el-menu-item>
                <el-menu-item index="workorderlists">工单列表</el-menu-item>
              <!--  <el-menu-item index="workorderdetails">工单详情</el-menu-item>-->
            </el-submenu>
            <el-submenu index="7">
                <template slot="title" ><i class="el-icon-date"></i>表单设计</template>
                <el-menu-item index="homeform">表单列表</el-menu-item>
              <!--  <el-menu-item index="designform">组件列表</el-menu-item>
                <el-menu-item index="formdesign">添加表单</el-menu-item>
                <el-menu-item index="editform">编辑表单</el-menu-item>
                <el-menu-item index="forminfo">表单详情</el-menu-item>-->
            </el-submenu>
        </el-menu>
    </div>
</template>

<script>
    export default {
        computed:{
            onRoutes(){
                return this.$route.path.replace('/','');
            }
        }
    }
</script>

<style scoped>
    .sidebar{
        display: block;
        position: absolute;
        width: 150px;
        left: 0;
        top: 70px;
        bottom:0;
        background: #2E363F;
    }
    .sidebar > ul {
        height:100%;
    }
</style>
