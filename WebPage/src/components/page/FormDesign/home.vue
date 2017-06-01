<template>
    <div class="home">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-date"></i> 表单设计</el-breadcrumb-item>
                <el-breadcrumb-item>表单列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <el-row :gutter="20" class="title-button">
            <el-col :span="6" :offset="18" class="text-right">
                <el-button  type="primary" class="grid-content bg-purple-light border-b-gray" @click="showDialog">新建表单</el-button>
            </el-col>
        </el-row >
        <el-row :gutter="24">
            <el-col :span="22">
                <el-form :inline="true" :model="formSearch" :rules="ruleSearch" ref="formSearch" class="demo-form-inline">
                    <el-form-item  id="search-input">
                        <el-input  v-model="list.search" placeholder="请输入搜索关键字" prop="search"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="getList()">搜索</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
            <el-col :span="2" class="text-center">
                <el-button  type="primary" class="grid-content bg-purple-light">查询器</el-button>
            </el-col>
        </el-row>

    <el-row>
        <el-col :span="24">
            <el-table id="table"
                      :span="24"
                      v-loading="loading2"
                      element-loading-text="拼命加载中"
                      :data="formList"
                      border
                      style="width: 100%"
                      :row-class-name="tableRowClassName"
                      :default-sort = "{prop: 'serial', order: 'descending'}">
                <el-table-column prop="name" label="表单名称"  :span="5"></el-table-column>
                <el-table-column prop="remark" label="表单说明" :span="5"></el-table-column>
                <el-table-column prop="enabled_value" label="表单状态" :span="5"></el-table-column>
                <el-table-column prop="modified_time" sortable label="最后更新" :span="5"></el-table-column>
                <el-table-column label="操作" width="" :span="5">
                    <template scope="scope">
                        <el-button size="small" type="info"
                                   @click="open(scope.row.id)">详情</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="block">
                <el-pagination
                    @size-change="handleSizeChange"
                    @current-change="handleCurrentChange"
                    :current-page="list.page"
                    :page-sizes="[5, 10, 15]"
                    :page-size="list.num"
                    layout="total, sizes, prev, pager, next, jumper"
                    :total="page.count">
                </el-pagination>
            </div>
        </el-col>
    </el-row>



        <!-- 生成表单 -->
        <el-dialog title="新建表单" :visible.sync="workOrderForm">
            <el-form :model="createForm" :rules="rulesCreate" ref="createForm" label-width="150px" class="demo-ruleForm">


                <el-form-item label="表单名称" prop="name">
                    <el-input v-model="createForm.name" placeholder="请输入表单名称" >  </el-input>

                </el-form-item>
                <el-form-item label="可用状态" prop="status">
                    <el-radio class="radio" v-model="createForm.status" label="1">启用</el-radio>
                    <el-radio class="radio" v-model="createForm.status" label="0">停用</el-radio>
                </el-form-item>

                <el-form-item label="表单说明" prop="info">
                    <el-input type="textarea" :rows="5" placeholder="请输入表单说明" v-model="createForm.info"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="closeDialog">取 消</el-button>
                <el-button type="primary" @click="upOrder('createForm')">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>
<script>
    import qs from 'qs'
    import message from 'element-ui'
    export default {
        data() {
            return {
                list:{              // 列表信息
                  search:'',        // 搜索
                    num:10,         // 显示数量
                    page:1         // 显示页
                },

                tableData: [],     // 数据列表
                page: [],          // 分页数据
                loading2: true,    // 加载动画

                dialogFormVisible: false,    // 编辑

                //  工单
                formSearch:{
                    priority:'',
                    product:'',
                    status:'',
                    date1:'',
                    date2:'',
                    search:''
                },
                ruleSearch:{
                    priority:[],
                    product:[],
                    status:[],
                    date1:[],
                    date2:[],
                    search:[]
                },


                //  表单
                formList:[],
                workOrderForm:false,     //  生成表单
               createForm:{
                    name:'',          //  表单名称
                    info:'',          //  表单说明
                    status:'',        //  可用状态

                },
                rulesCreate:{
                    name: [
                        {required: true, message: '请输入表单名称', trigger: 'change,blur'}
                    ],
                    info:[
                        {required: true, message: '请输入详细的表单说明', trigger: 'change,blur'}
                    ],
                    status:[
                        {required: true, message: '请选择可用状态', trigger: 'blur,change'}
                    ],
                }



            }

        },
        created() {
            this.getList();

        },
        methods: {
            // 获取列表数据
            getList() {
                let self = this;
                this.$axios.post('/makesCanton/Opera/Predefined/formList',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    search:self.list.search,
                    num:self.list.num,
                    page:self.list.page})).then( (res) =>{
                    self.loading2 = false;
                    console.log(res);
                    if(res.data.status == 100){
                        self.formList = res.data.value;
                        self.page = res.data.page;
                        self.page.count = Number(self.page.count);
                    }else if(res.data.status == 1011){
                        self.$message({
                            type: 'success',
                            message: '权限不足，请与管理员联系'
                        });
                    }else  if(res.data.status == 1012){
                        self.$router.push('/login');
                    }
                })
            },
            // 切换每页显示数量
            handleSizeChange(val) {
                this.list.num = val;
                this.getList();
            },
            // 切换页码
            handleCurrentChange(val) {
                this.list.page = val;
                this.getList();
            },
            tableRowClassName(row, index) {
                if (index === 1) {
                    return 'info-row';
                } else if (index === 3) {
                    return 'positive-row';
                }
                return '';
            },
            editNo() {
                let self = this;
                self.dialogFormVisible = false;
            },


            //工作任务单
            showDialog() {          //打开表单
                let self = this;
                self.workOrderForm = true;
            },
            closeDialog() {         //关闭表单
                let self = this;
                self.workOrderForm = false;
            },
            closeDialogInfo() {         //关闭表单
                let self = this;
                self.dialogFormVisible = false;
            },
            upOrder(formName) {
                let self = this;
                this.$refs[formName].validate((valid) => {
                    if(valid){
                        this.$axios.post('/makesCanton/Opera/Predefined/editAdd', qs.stringify({
                            key: self.$cookie.get('oKey'),
                            user_id: self.$cookie.get('token'),
                            name: self.createForm.name,
                            remark: self.createForm.info,
                            enabled:self.createForm.status
                        })).then((res) => {
                            if (res.data.status == 100) {
                                self.getList();
                                self.workOrderForm = false;
                                self.createForm= {
                                    name: '',          //  销售渠道
                                    info: '',          //  产品类型
                                    status: '',       //  客户名称
                                };
                                this.$message({
                                    message: "提交成功",
                                    type: 'success'
                                });
                            }else if(res.data.status == 1011){
                                self.$message({
                                    type: 'success',
                                    message: '权限不足，请与管理员联系'
                                });
                            }else if (res.data.status == 1012) {
                                self.$router.push('/login');
                            }else {
                                self.$message({
                                    type: 'success',
                                    message: res.data.msg
                                });
                            }

                        })
                    }else {

                        return false;
                    }
                })
            },
            open(id){
                this.$router.push({ path: 'designform',query: { form_id: id }})
            },
            onSubmit(){

            }
        },
        watch: {
            '$route' (to, from) {
            }
        },
        mounted() {

        }
    }
</script>
<style scoped>
    .el-table .info-row {
        background: #c9e5f5;
    }
    #table .el-table .cell,
    .el-table th>div{
        padding-right: 0;
        padding-left: 0;
    }
    .el-table, .el-table .cell{
        text-align: center;
    }
    .el-table .positive-row {
        background: #e2f0e4;
    }
    .block{
        margin-top: 20px;
    }
    .el-row{
        margin-bottom: 20px;
    }
    .el-dialog__header{
        background-color: #20a0ff;
    }
    #search-input .el-form-item__content{
        width: 100%;
    }
    .title-button{
        border-bottom: 1px solid #ccc;
        padding-bottom: 15px;
    }
</style>

