<template>
    <el-row>
        <el-col :span="24">
            <div class="title"><i class="el-icon-arrow-left" @click="comeback"></i>文本类型</div>
        </el-col>
        <el-col :span="12">
            <el-form :model="form" ref="formClose" label-width="150px" class="demo-ruleForm">
                <el-form-item label="字段名称" class="text-right" prop="name"
                              :rules="[{ required: true, message: '请输入字段名称', trigger: 'blur' }]">
                    <el-input v-model="form.name"></el-input>
                </el-form-item>

                <el-form-item label="字段说明" prop="info"
                              :rules="[{ required: true, message: '请输入字段描述', trigger: 'blur' }]">
                    <el-input v-model="form.info"></el-input>
                </el-form-item>

                <el-form-item label="文本长度" prop="length"
                              :rules="[{ required: true, message: '请输入文本长度' },
                               { type: 'number', message: '文本长度必须为数字值'}]">
                    <el-input v-model.number="form.length" type="number"></el-input>
                </el-form-item>

                <el-form-item label="是否必填" prop="is_require"
                              :rules="[{ required: true, message: '请选择是否必填', trigger: 'blur' }]">
                    <el-radio class="radio" v-model="form.is_require" label="1">是</el-radio>
                    <el-radio class="radio" v-model="form.is_require" label="2">否</el-radio>
                </el-form-item>

                <el-form-item label="默认值" >
                    <el-input v-model="form.info"></el-input>
                </el-form-item>

                <el-form-item class="titles" id="titles" label-width="0px">
                    <el-row class="title">
                        <el-col :span="12">选项设置</el-col>
                        <el-col :span="12" class="text-right add"><span  @click="addDomain">+</span></el-col>
                    </el-row>
                </el-form-item>

                <el-form-item
                    v-for="(domain, index) in form.domains"
                    :label="'选项' + index"
                    :key="domain.key"
                    :prop="'domains.' + index + '.value'"
                    :rules="{required: true, message: '域名不能为空', trigger: 'blur'}">

                    <el-input v-model="domain.value" style="width: 80%"></el-input>
                    <el-button @click.prevent="removeDomain(domain)">删除</el-button>
                </el-form-item>

               <!-- <el-form-item>
                    <el-button type="primary" @click="submitForm('form')">提交</el-button>
                    <el-button @click="addDomain">新增域名</el-button>
                    <el-button @click="resetForm('dynamicValidateForm')">重置</el-button>
                </el-form-item>-->

            </el-form>

            <!--<el-form :model="form" :rules="rulesForm" ref="formClose" label-width="150px" class="demo-ruleForm">
                <el-form-item label="选项" class="text-right" prop="name" v-for="item in ">
                    <el-input v-model="form.name"></el-input>
                </el-form-item>
            </el-form>-->

            <el-form :model="form" ref="form" label-width="100px" class="demo-dynamic">
               <!-- <el-form-item
                    prop="email"
                    label="邮箱"
                    :rules="[
                      { required: true, message: '请输入邮箱地址', trigger: 'blur' },
                      { type: 'email', message: '请输入正确的邮箱地址', trigger: 'blur,change' }
                    ]"
                    >
                    <el-input v-model="dynamicValidateForm.email"></el-input>
                </el-form-item>-->

              <!--  <el-form-item
                    v-for="(domain, index) in form.domains"
                    :label="'选项' + index"
                    :key="domain.key"
                    :prop="'domains.' + index + '.value'"
                    :rules="{required: true, message: '域名不能为空', trigger: 'blur'}">

                    <el-input v-model="domain.value" style="width: 80%"></el-input>
                    <el-button @click.prevent="removeDomain(domain)">删除</el-button>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="submitForm('form')">提交</el-button>
                    <el-button @click="addDomain">新增域名</el-button>
                    <el-button @click="resetForm('dynamicValidateForm')">重置</el-button>
                </el-form-item>-->
            </el-form>
            <div  class="form-footer">
                <el-button @click="comeback">取 消</el-button>
                <el-button type="primary" @click="upData('formClose')">保 存</el-button>
            </div>

        </el-col>
    </el-row>
</template>

<script>
    import qs from 'qs'
    import { message } from 'element-ui';
    import Sortable from 'sortablejs';
    export default {
        data: function(){
            return {
                ruleForm: {
                    name:''
                },
                rules:{
                    name: [
                        { required: true, message: '请输入活动名称', trigger: 'blur' },

                    ],
                },
                form:{
                    name:'',               //组件名字
                    info:'',
                    is_require:'',         // 是否必填
                    length:'',             // 字段长度
                    domains: [{
                        value: ''
                    }],
                },
                /*rulesForm:{
                    name:[
                        {required: true, message: '请输入标题', trigger: 'blur'},
                    ],
                    info:[
                        {required: true, message: '请输入销售渠道', trigger: 'blur'},
                    ],
                    isrequrie:[
                        {required: true, message: '请选择是否必填', trigger: 'change'},
                    ]
                },*/


              /*  dynamicValidateForm: {
                    domains: [{
                        value: ''
                    }],
                    email: ''
                }*/

            }
        },
        created(){
            let self = this;
            switch (this.$route.query.type){
                case 'text':
                    self.form.type == 1;     // 单行文本
                    break;
                case 'select':
                    self.form.type == 2;     // 下拉列表
                    break;
                case 'date':
                    self.form.type == 3;    // 日期
                    break;
                case 'time':
                    self.form.type == 4;    // 时间
                    break;
                case 'datetime':
                    self.form.type == 5;    // 日期时间
                    break;
                case 'decimal':
                    self.form.type == 6;     // 小数
                    break;
                case 'number':
                    self.form.type == 7;    // 整数
                    break;
                case 'radio':
                    self.form.type == 8;    // 单选框
                    break;
                case 'checkbox':
                    self.form.type == 9;    // 多选框
                    break;
                case 'textarea':
                    self.form.type == 10;   // 多行文本
                    break;
            }
        },
        methods: {
            comeback() {
                let self = this;
                self.$router.push('/formdesign');
                self.form = {
                    name:'',
                    info:'',
                    isrequrie:'',
                }
            },
            submitForm(ruleForm) {
                this.$refs[ruleForm].validate((valid) => {
                    if (valid) {
                        alert('submit!');
                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            upData1(formClose){
                let self = this;
                console.log(self.form);
                this.$refs[formClose].validate((valid) => {
                    if(valid){
                        alert('form');
                    }
                })
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
            upData(formClose){
                let self = this;
                console.log(self.form);
                this.$refs[formClose].validate((valid) => {
                    if(valid){
                        this.$axios.post('/makesCanton/Opera/Predefined/editAttrInster',qs.stringify({
                            key:self.$cookie.get('oKey'),
                            user_id:self.$cookie.get('token'),
                            form_id:self.$route.query.id,
                            com_type:self.form.type,
                            name:self.form.name,
                            is_require:self.form.is_require,
                            length:self.form.length
                        })).then( (res) =>{
                            console.log(res);
                            if(res.data.status == 100){
                                self.$message({
                                    type: 'success',
                                    message: '操作完成'
                                });
                                self.open();
                            }else if(res.data.status == 1011){
                                self.$message({
                                    type: 'success',
                                    message: '权限不足，请与管理员联系'
                                });
                            }else  if(res.data.status == 1012){
                                self.$router.push('/login');
                            }
                        })
                    }
                })
            },
            resetForm(formName) {
                this.$refs[formName].resetFields();
            },
            removeDomain(item) {
                let self = this;
                let index = this.form.domains.indexOf(item);
                if(index == 0){
                    self.$message({
                        type: 'success',
                        message: '至少填一个'
                    });
                }else {
                    this.form.domains.splice(index, 1)
                }
            },
            addDomain() {
                this.form.domains.push({
                    value: '',
                    key: Date.now()
                });
            },
            comeback() {
                let self = this;
                self.$router.push({path: '/designform',query: {id: self.$route.query.id}});
            }
        }
    }
</script>

<style scoped>
    .title{
        padding: 15px 10px;
        border-bottom: 1px solid #ccc;
        color: #00B38B
    }
    .el-form{
        margin-top: 10px;
    }
    .form-footer{
        margin-left: 50px;
    }
    .add{
        font-size: 30px;
        cursor: pointer;
    }
#title .el-form-item__content{
    margin-left: 0;
}
</style>
