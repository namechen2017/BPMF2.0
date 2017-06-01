<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-date"></i> 工单</el-breadcrumb-item>
                <el-breadcrumb-item>工单任务台</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="ms-doc">
            <el-row :gutter="20" class="">
                <el-col :span="8">
                    <div class="grid-content bg-purple">0</div>
                    <div class="grid-content bg-purple">未分配的任务</div>
                </el-col>
                <el-col :span="8">
                    <div class="grid-content bg-purple">0</div>
                    <div class="grid-content bg-purple">处理中的工单</div>
                </el-col>
                <el-col :span="8">
                    <div class="grid-content bg-purple">0</div>
                    <div class="grid-content bg-purple">已完成的工单</div>
                </el-col>
            </el-row>
        </div>
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span style="line-height: 36px;">我的未关闭的工单</span>
            </div>
            <el-table
                v-loading="loading2"
                element-loading-text="拼命加载中"
                :data="tableData2"
                border
                style="width: 100%"
                :row-class-name="tableRowClassName">
                <el-table-column prop="date" label="产品类型" width="200"></el-table-column>
                <el-table-column prop="name" label="主题" width="200"></el-table-column>
                <el-table-column prop="address" label="客户名称" width="270"></el-table-column>
                <el-table-column prop="address" label="优先级" width="270"></el-table-column>
                <el-table-column prop="address" label="状态" width="270"></el-table-column>
                <el-table-column prop="address" label="负责人" width="220"></el-table-column>
                <el-table-column prop="address" label="最后更新时间" width="200"></el-table-column>
            </el-table>
        </el-card>
    </div>
</template>

<script>

    export default {
        data: function(){
            return {
                formInline: {
                    prefix: '',  // 编号前缀
                    date: '',    // 日期格式
                    serial: ''    // 流水号位数
                },
                tableData2: [{
                    date: '2016-05-02',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1518 弄',
                }, {
                    date: '2016-05-04',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1518 弄'
                }, {
                    date: '2016-05-01',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1518 弄',
                }, {
                    date: '2016-05-03',
                    name: '王小虎',
                    address: '上海市普陀区金沙江路 1518 弄'
                }],
                loading2: false
            }
        },
        methods: {

        onSubmit() {
            console.log('submit!');
        },
        tableRowClassName(row, index) {
            if (index === 1) {
                return 'info-row';
            } else if (index === 3) {
                return 'positive-row';
            }
                return '';
            }
        }
    }
</script>

<style scoped>
    .el-row {
        margin-bottom: 20px;
        text-align: center;
    }

    .el-col {
        border-radius: 4px;
    }
    .el-col div:nth-child(1){
       line-height: 100px;
        font-size: 50px;
        padding-top: 50px;
    }
    .el-col div:nth-child(2){
        font-size: 20px;
    }
    .bg-purple-dark {
        background: #99a9bf;
    }
    .bg-purple {
        background: #d3dce6;
    }
    .bg-purple-light {
        background: #e5e9f2;
    }
    .grid-content {
        border-radius: 4px;
        min-height: 100px;
    }
    .row-bg {
        padding: 10px 0;
        background-color: #f9fafc;
    }
</style>
