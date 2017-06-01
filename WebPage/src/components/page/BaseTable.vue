<template>
    <div class="table" id="table">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-menu"></i> 表格</el-breadcrumb-item>
                <el-breadcrumb-item>基础表格</el-breadcrumb-item>
            </el-breadcrumb>
        </div>

        <el-table v-loading="loading2"
                  element-loading-text="拼命加载中" :data="tableData" border style="width: 100%">
            <el-table-column type="index" label="序号"  width="100">
            </el-table-column>
            <el-table-column prop="apply_name" label="申请人"  width="100">
            </el-table-column>
            <el-table-column prop="com_time" label="要求完成时间"  width="150">
            </el-table-column>
            <el-table-column prop="assign_region" label="指定地域"  width="150">
            </el-table-column>
            <el-table-column prop="custom_name" label="客户名称"  width="250">
            </el-table-column>
            <el-table-column prop="contact_people" label="联系人" width="100">
            </el-table-column>
            <el-table-column prop="tel_phone" label="联系电话" width="150">
            </el-table-column>
            <el-table-column prop="executor_name" label="执行人" width="100">
            </el-table-column>
            <el-table-column prop="execut_time" label="执行时间" width="200">
            </el-table-column>
            <el-table-column prop="created_time" label="实际完成时间" width="200">
            </el-table-column>
            <el-table-column prop="expiry_time" label="vps到期日" width="120">
            </el-table-column>

            <el-table-column label="操作" width="200">
                <template scope="scope">
                    <!--<el-button size="small"
                            @click="handleEdit(scope.$index, scope.row)">编辑</el-button>
                    <el-button type="small"
                               @click="open3">详情</el-button>-->
                    <el-button type="small"
                                @click="open4(scope.row)">修改</el-button>
                    <el-button size="small" type="danger"
                            @click="handleDelete(scope.$index, scope.row)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>

        <el-dialog title="信息详情" :visible.sync="dialogFormVisible">
            <el-form :model="ruleForm2" :rules="rules2" ref="ruleForm2" label-width="100px" class="demo-ruleForm">
                <el-form-item label="密码" prop="pass">
                    <el-input type="password" v-model="ruleForm2.pass" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="确认密码" prop="checkPass">
                    <el-input type="password" v-model="ruleForm2.checkPass" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="年龄" prop="age">
                    <el-input v-model.number="ruleForm2.age"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="submitForm('ruleForm2')">提交</el-button>
                    <el-button @click="resetForm('ruleForm2')">重置</el-button>
                </el-form-item>
            </el-form>

            <!--<el-form :model="form" :rules="rules" ref="form">
                <el-form-item label="日期" :label-width="formLabelWidth" prop="date">
                    <el-date-picker type="date" placeholder="选择日期"
                                    v-model="form.date" style="width: 100%;" auto-complete="off"></el-date-picker>
                    &lt;!&ndash;<el-input  v-model.date="form.date" auto-complete="off"></el-input>&ndash;&gt;
                </el-form-item>
                <el-form-item label="姓名" :label-width="formLabelWidth" prop="name">
                    <el-input v-model="form.name" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="地址" :label-width="formLabelWidth" prop="address">
                    <el-input v-model="form.address" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="头像" :label-width="formLabelWidth" prop="logo">
                    <el-input v-model="form.logo" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="活动区域" :label-width="formLabelWidth">
                    <el-select v-model="form.region" placeholder="请选择活动区域">
                        <el-option label="区域一" value="shanghai"></el-option>
                        <el-option label="区域二" value="beijing"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="submitForm('form')">立即创建</el-button>
                    <el-button @click="resetForm('form')">重置</el-button>
                </el-form-item>
            </el-form>-->
           <!-- <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="submitForm">确 定</el-button>
            </div>-->
        </el-dialog>

        <div class="pagination">
            <el-pagination
                    @current-change ="handleCurrentChange"
                    layout="prev, pager, next"
                    :total="page.count_page">
            </el-pagination>
        </div>
        <!--<div class="block">
            <span class="demonstration">完整功能</span>
            <el-pagination
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
                :current-page="currentPage4"
                :page-sizes="[100, 200, 300, 400]"
                :page-size="100"
                layout="total, sizes, prev, pager, next, jumper"
                :total="400">
            </el-pagination>
        </div>-->
    </div>
</template>

<script>
    import qs from 'qs'
    export default {
        data() {
            var checkAge = (rule, value, callback) => {
                if (!value) {
                    return callback(new Error('年龄不能为空'));
                }
                setTimeout(() => {
                    if (!Number.isInteger(value)) {
                        callback(new Error('请输入数字值'));
                    } else {
                        if (value < 18) {
                            callback(new Error('必须年满18岁'));
                        } else {
                            callback();
                        }
                    }
                }, 1000);
            };
            var validatePass = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('请输入密码'));
                } else {
                    if (this.ruleForm2.checkPass !== '') {
                        this.$refs.ruleForm2.validateField('checkPass');
                    }
                    callback();
                }
            };
            var validatePass2 = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('请再次输入密码'));
                } else if (value !== this.ruleForm2.pass) {
                    callback(new Error('两次输入密码不一致!'));
                } else {
                    callback();
                }
            };
            return {
                tableData: [],
                loading2: true,// 加载动画
                page:[],       // 分页数据
                cur_page: 1,
                dialogTableVisible: false,
                dialogFormVisible: false,
                form: {
                    name: '',
                    date:'',
                    address:'',
                    logo:'',
                    region: '',
                    date1: '',
                    date2: '',
                    delivery: false,
                    type: [],
                    resource: '',
                    desc: ''
                },
                formLabelWidth: '120px',
                ruleForm2: {
                    pass: '',
                    checkPass: '',
                    age: ''
                },
                rules2: {
                    pass: [
                        { validator: validatePass, trigger: 'blur' }
                    ],
                    checkPass: [
                        { validator: validatePass2, trigger: 'blur' }
                    ],
                    age: [
                        { validator: checkAge, trigger: 'blur' }
                    ]
                },
                oKey:'',
                token:''
            }

                /*rules: {
                    name: [
                        { required: true, message: '请输入活动名称', trigger: 'blur' },
                        { min: 3, max: 5, message: '长度在 3 到 5 个字符', trigger: 'blur' }
                    ],
                    address: [
                        { required: true, message: '请选择活动区域', trigger: 'change' }
                    ],
                    date: [
                        { type: 'date', required: true, message: '请选择日期', trigger: 'change' }
                    ],
                    lo: [
                        { type: 'date', required: true, message: '请选择时间', trigger: 'change' }
                    ],
                    type: [
                        { type: 'array', required: true, message: '请至少选择一个活动性质', trigger: 'change' }
                    ],
                    logo: [
                        { required: true, message: '请选择活动资源', trigger: 'change' }
                    ],
                    desc: [
                        { required: true, message: '请填写活动形式', trigger: 'blur' }
                    ]
                }*/

        },
        created(){
            //this.getData();
        },
        methods: {
            handleCurrentChange(val){
                this.cur_page = val;
                this.getData();
            },
            getData(){
                let self = this;
                 self.okey = self.$cookie.get('oKey');
                 self.token = self.$cookie.get('token');
                this.$axios.post('/api/ApplyVps/vpsList',qs.stringify({
                    key:self.okey,
                    user_id:self.token,
                    num:10,
                    page:1,})).then(function (res){
                    self.loading2 = false;
                    console.log(res);
                    if(res.data.status == 1012){
                        self.$router.push('/login');
                    }
                    self.tableData = res.data.value;
                    self.page = res.data.page;
                })
            },
            formatter(row, column) {
                return row.address;
            },
            filterTag(value, row) {
                return row.tag === value;
            },
            handleEdit(index, row) {
                this.$message('编辑第'+(index+1)+'行'+row.name);
                console.log(row);
                console.log(row.name);
            },
            handleDelete(index, row) {
                this.$message.error('删除第'+(index+1)+'行');
            },
            open3() {
                this.$prompt('请输入邮箱', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    inputPattern: /[\w!#$%&'*+/=?^_`{|}~-]+(?:\.[\w!#$%&'*+/=?^_`{|}~-]+)*@(?:[\w](?:[\w-]*[\w])?\.)+[\w](?:[\w-]*[\w])?/,
                    inputErrorMessage: '邮箱格式不正确'
                }).then(({ value }) => {
                    this.$message({
                        type: 'success',
                        message: '你的邮箱是: ' + value
                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '取消输入'
                    });
                });
            },
            open4(row){
                let self = this;
                self.dialogFormVisible = true;
                console.log(row);
                self.form = row;
            },
            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        alert('submit!');
                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            resetForm(formName) {
                this.$refs[formName].resetFields();
            }
        }
    }
</script>

<style >
    #table .el-table .cell,
    #table .el-table th>div{
        padding-left: 0;
        padding-right: 0;
    }
</style>
