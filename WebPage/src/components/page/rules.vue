<template>
    <div class="rules">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-menu"></i> 编号</el-breadcrumb-item>
                <el-breadcrumb-item>编号规则-编号列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <el-form :inline="true" :model="formInline" :rules="rules" ref="formInline" class="demo-form-inline">
            <el-form-item label="规则名" prop="title">
                <el-input v-model="formInline.title" placeholder="规则名"></el-input>
            </el-form-item>
            <el-form-item label="编号前缀" prop="prefix">
                <el-input v-model="formInline.prefix" placeholder="建议为规则名的中或英文首字母"></el-input>
            </el-form-item>
            <el-form-item label="日期格式" prop="date_type">
                <el-select v-model="formInline.date_type" placeholder="日期格式">
                    <el-option label="年" value="YYYY"></el-option>
                    <el-option label="年-月" value="YYYYMM"></el-option>
                    <el-option label="年-月-日" value="YYYYMMDD"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="流水号位数" prop="serial" style="width: 400px">
                <el-input v-model.number="formInline.serial" type="number" max="9" min="1" placeholder="流水号位数" style="width: 300px"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="onSubmit('formInline')">生成</el-button>
            </el-form-item>
        </el-form>

        <el-table
            v-loading="loading2"
            element-loading-text="拼命加载中"
            :data="tableData"
            border
            style="width: 100%"
            :row-class-name="tableRowClassName"
            :default-sort = "{prop: 'serial', order: 'descending'}">
            <el-table-column type="index" label="序号" width="180"></el-table-column>
            <el-table-column prop="title" label="规则名" width="180"></el-table-column>
            <el-table-column prop="prefix" label="前缀" width="180"></el-table-column>
            <el-table-column prop="date_type" label="日期类型" width="180"></el-table-column>
            <el-table-column prop="serial" sortable label="流水号位数" ></el-table-column>

           <!-- <el-table-column min-width="300px" label="标题">
                <template scope="scope">
                    <el-input v-show="editShow" size="small" v-model="scope.row.title"></el-input>
                    <span v-show="!editShow">{{ scope.row.title }}</span>
                </template>
            </el-table-column>-->

            <el-table-column label="操作" width="250">
                <template scope="scope">
                    <el-button type="small"  icon="edit"
                               @click="editData(scope.row.id)">编辑</el-button>
                    <el-button size="small" type="danger"  icon="delete"
                               @click="deleteData(scope.row.id)">删除</el-button>
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

        <el-dialog title="信息详情" :visible.sync="dialogFormVisible">
            <el-form :model="formInlineEdit" :rules="rules" ref="formInlineEdit" label-width="100px" class="demo-ruleForm">
                <el-form-item label="规则名" prop="title">
                    <el-input v-model="formInlineEdit.title" placeholder="规则名"></el-input>
                </el-form-item>
                <el-form-item label="编号前缀" prop="prefix">
                    <el-input v-model="formInlineEdit.prefix" placeholder="建议为规则名的中或英文首字母"></el-input>
                </el-form-item>
                <el-form-item label="日期格式" prop="date_type">
                    <el-select v-model="formInlineEdit.date_type" placeholder="日期格式">
                        <el-option label="年" value="YYYY"></el-option>
                        <el-option label="年-月" value="YYYYMM"></el-option>
                        <el-option label="年-月-日" value="YYYYMMDD"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="流水号位数" prop="serial">
                    <el-input v-model.number="formInlineEdit.serial" type="number" max="9" min="1" placeholder="流水号位数"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="editNo">取 消</el-button>
                <el-button type="primary" @click="deleteDatas('formInlineEdit')">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>
<script>
    import qs from 'qs'
    export default {
        data() {
            return {
                formInline: {         // 编号生成信息
                    title:'',         // 规则名
                    prefix: '',       // 编号前缀
                    date_type: '',    // 日期格式
                    serial:'',         // 流水号位数
                    id:''
                },
                formInlineEdit: {         // 编号生成信息
                    title:'',         // 规则名
                    prefix: '',       // 编号前缀
                    date_type: '',    // 日期格式
                    serial:'',         // 流水号位数
                    id:''
                },
                rules:{               // 编号生成信息规则
                    title: [
                        {required: true, message: '请输入规则名', trigger: 'blur'}
                        ],
                    prefix: [
                        {required: true, message: '请输入编码前缀', trigger: 'blur'}
                        ],
                    date_type: [
                        {required: true, message: '请输入日期格式', trigger: 'blur'}
                    ],
                    serial: [
                        {type:'number', required: true, message: '请输入流水号位数', trigger: 'blur'},
                        { type:'number',min: 1, max: 9, message: '数字范围为1~9', trigger: 'blur' }
                    ]
                },
                list:{              // 列表信息
                  search:'',        // 搜索
                    num:10,         // 显示数量
                    page:1         // 显示页
                },
                tableData: [],     // 数据列表
                page: [],          // 分页数据
                loading2: true,    // 加载动画
               /* editShow:false,        // 是否可编辑*/
                dialogFormVisible: false,    // 编辑
            }

        },
        created() {
            this.getList();
        },
        methods: {
            // 获取列表数据
            getList() {
                let self = this;
                this.$axios.post('/makesCanton/Opera/Number/listNumberRule',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    search:self.list.search,
                    num:self.list.num,
                    page:self.list.page})).then( (res) =>{
                    self.loading2 = false;
                    if(res.data.status == 1012){
                        self.$router.push('/login');
                    }
                    self.tableData = res.data.value;
                    self.page = res.data.page;
                    self.page.count = Number(self.page.count);
                })
            },
            // 生成编号
            onSubmit(formInline) {
                let self = this;
                this.$refs[formInline].validate((valid) => {
                    if (valid) {

                        this.$axios.post('/makesCanton/Opera/Number/createNumberRule',qs.stringify({
                            key:self.$cookie.get('oKey'),
                            user_id:self.$cookie.get('token'),
                            title:self.formInline.title,
                            prefix:self.formInline.prefix,
                            date_type:self.formInline.date_type,
                            serial:self.formInline.serial})).then( (res) =>{

                            this.$message({
                                message: res.data.msg,
                                type: 'success'
                            });
                            if(res.data.status == 100){
                                self.getList();
                            }
                            if(res.data.status == 1012){
                                self.$router.push('/login');
                            }
                            self.tableData = res.data.value;
                            self.page = res.data.page;
                        })
                    } else {
                        return false;
                    }
                });


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
            editData(id) {
                let self = this;
                self.dialogFormVisible = true;
                this.$axios.post('/makesCanton/Opera/Number/listNumberRule',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    search:self.list.search,
                    num:self.list.num,
                    page:self.list.page,
                    id:id
                    })).then( (res) =>{
                    self.loading2 = false;
                    if(res.data.status == 1012){
                        self.$router.push('/login');
                    }
                    if(res.data.status == 100){
                        self.formInlineEdit = res.data.value[0];
                        self.formInlineEdit.serial = Number(self.formInlineEdit.serial);
                    }

                })
            },
            deleteData(id) {
                    var self = this;
                    this.$confirm('是否删除该条信息？', '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(() => {
                        this.$axios.post('/makesCanton/Opera/Number/deleteNumberRule',qs.stringify({
                            key:self.$cookie.get('oKey'),
                            user_id:self.$cookie.get('token'),
                            id:id})).then( (res) =>{
                            self.loading2 = false;
                            if(res.data.status == 100){
                                self.getList();
                                setTimeout(function () {
                                    self.$message({
                                        type: 'success',
                                        message: '删除成功!'
                                    })
                                },1000)
                            }
                            if(res.data.status == 1012){
                                self.$router.push('/login');
                            }

                        })

                    }).catch(() => {
                        this.$message({
                            type: 'info',
                            message: '已取消删除'
                        });
                    });
            },
            deleteDatas(formInlineEdit) {
               /* Opera/Number/editNumberRule*/
                let self = this;
                this.$refs[formInlineEdit].validate((valid) => {
                    if(valid) {
                        this.$axios.post('/makesCanton/Opera/Number/editNumberRule', qs.stringify({
                            key: self.$cookie.get('oKey'),
                            user_id: self.$cookie.get('token'),
                            title: self.formInlineEdit.title,
                            prefix: self.formInlineEdit.prefix,
                            date_type: self.formInlineEdit.date_type,
                            id: self.formInlineEdit.id,
                            serial: self.formInlineEdit.serial
                        })).then((res) => {
                            if (res.data.status == 100) {
                                self.getList();
                                self.dialogFormVisible = false;
                                this.$message({
                                    message: "修改成功",
                                    type: 'success'
                                });
                            }else if(res.data.status == 102){
                                this.$message({
                                    message: res.data.msg,
                                    type: 'success'
                                });
                            }
                            if (res.data.status == 1012) {
                                self.$router.push('/login');
                            }
                        })
                    }
                })
            },
            editNo() {
                let self = this;
                self.dialogFormVisible = false;
            }
        }
    }
</script>
<style scoped>
    .el-table .info-row {
        background: #c9e5f5;
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
</style>

