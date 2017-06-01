<template>
    <el-row>
        <el-breadcrumb separator="/">
            <el-breadcrumb-item><i class="el-icon-date"></i> 表单设计</el-breadcrumb-item>
            <el-breadcrumb-item>表单设计-{{textType}}</el-breadcrumb-item>
        </el-breadcrumb>
        <el-col :span="24">
            <div class="title"><i class="el-icon-arrow-left" @click="comeback"></i>文本类型</div>
        </el-col>
        <el-col :span="12">
            <el-form :model="form" ref="formClose" label-width="150px" class="demo-ruleForm border-b-gray">
                <el-form-item label="字段名称" class="text-right" prop="name"
                              :rules="[{ required: true, message: '请输入字段名称', trigger: 'blur,change' }]">
                    <el-input v-model="form.name"></el-input>
                </el-form-item>

                <!-- <el-form-item label="字段标题" class="text-right"
                               :rules="[{ required: true, message: '请输入字段标题', trigger: 'blur' }]">
                     <el-input v-model="form.title"></el-input>
                 </el-form-item>-->

                <el-form-item label="字段说明" prop="remark"
                              :rules="[{ required: true, message: '请输入字段描述', trigger: 'blur,change' }]">
                    <el-input v-model="form.remark"></el-input>
                </el-form-item>

                <el-form-item label="是否必填" prop="is_require"
                              :rules="[{ required: true, message: '请选择是否必填', }]">
                    <el-radio class="radio" v-model="form.is_require" label="1">是</el-radio>
                    <el-radio class="radio" v-model="form.is_require" label="2">否</el-radio>
                </el-form-item>

                <el-form-item label="文本长度" prop="length"
                              :rules="[{ required: true, message: '请输入文本长度' },
                               { type: 'number', message: '文本长度必须为数字值'}]">
                    <el-input v-model.number="form.length" type="number"></el-input>
                </el-form-item>

                <el-form-item label="默认值" >
                    <el-input v-model="form.default_value"></el-input>
                </el-form-item>

                <el-form-item label="小数精度" v-show="form.type == '6'" :prop="form.type == 6 ? 'decimal':''"
                              :rules="[{ required: true, message: '请输入小数精度', trigger: 'blur, change' },
                               { type: 'number', message: '小数精度必须为数字值', trigger: 'blur, change'}]">
                    <el-input v-model.number="form.decimal" type="number"></el-input>
                </el-form-item>

                <el-form-item label="验证规则" v-show="!types"
                              :rules="[{ required: true, message: '请选择验证规则', trigger: 'blur,change' }]">
                    <el-select v-model="form.validation" placeholder="请选择验证规则">
                        <el-option :label="item.key_value" :value="item.key_name" v-for="item in rules"></el-option>
                    </el-select>
                </el-form-item>

                <!-- 下拉 单选 复选 -->
                <el-form-item class="titles" id="titles" label-width="0px" v-show="types">
                    <el-row class="title">
                        <el-col :span="12">选项设置</el-col>
                        <el-col :span="12" class="text-right add"><span  @click="addDomain">+</span></el-col>
                    </el-row>
                </el-form-item>

                <el-form-item v-show="types" class="clearfix"
                              v-for="(predefined, index) in form.predefined"
                              :label="'选项' + index"
                              :key="predefined.key"
                              :prop="types? 'predefined.' + index + '.value':''"
                              :rules="{required: true, message: '域名不能为空', trigger: 'blur,change'}">

                    <el-input v-model="predefined.value" style="width: 80%;float:left;"></el-input>
                    <div class="text-right" style="width: 20%;float: right;">
                        <el-button @click.prevent="removeDomain(predefined)">删除</el-button>
                    </div>
                </el-form-item>

            </el-form>

            <div  class="form-footer m-t-xs    ">
                <el-button type="primary" @click="upData('formClose')">保 存</el-button>
                <el-button @click="open">取 消</el-button>

            </div>
        </el-col>
    </el-row>
</template>

<script>
    import qs from 'qs'
    import { message } from 'element-ui';
    export default {
        data: function(){
            return {
                form:{
                    /*name:'',               // 字段名称
                    title:'',              // 字段标题
                    info:'',               // 字段描述
                    default_value:'',      // 默认值
                    length:'',             // 字段长度
                    accuracy:'',           // 小数精度
                    is_require:'',         // 是否必填
                    rules:'',              // 验证规则
                    type:'',               // 组件类型
                    domains: [
                        {            // 预定于内容
                        value: '',
                        key: Date.now()
                    }
                    ],*/

                    name:'',                            // 组件名
                    is_require:'',                     // 必填
                    length:'',                     　 // 数据长度
                    default_value: '',               // 默认值
                    accuracy: '',                   // 小数精度
                    validation: '',                // 验证规则
                    predefined:[],                // 预定义内容
                    sort:'',                     // 排序
                    remark:'',                  // 字段描述
                    type:'',                   // 组件类型
                },
                rules:[],
                type: true,
                types: false,               // 存不存在预定于内容（下拉 单选 复选）
                textType:'',
                list:[{
                    search: '',
                    num:1,
                    page:1
                }
                ],
                dataList:{
                    name:'',                            // 组件名
                    is_require:'',                     // 必填
                    length:'',                     　 // 数据长度
                    default_value: '',               // 默认值
                    accuracy: '',                   // 小数精度
                    validation: '',                // 验证规则
                    predefined_value:[],          // 预定义内容
                    sort:'',                     // 排序
                    remark:'',                  // 字段描述
                    type:'',                   // 组件类型
                }

            }
        },
        created(){
            this.getChannel();
            this.getList();
            let self =this;
            if(this.$route.query.type == 'select'|| this.$route.query.type == 'radio' || this.$route.query.type == 'checkbox') {
                this.type = !this.type;
            }
            switch (self.$route.query.type){
                case 'text':
                    self.form.type = 1;     // 单行文本
                    self.textType  = '单行文本';
                    break;
                case 'select':
                    self.form.type = 2;     // 下拉列表
                    self.textType  = '下拉列表';
                    break;
                case 'date':
                    self.form.type = 3;    // 日期
                    self.textType  = '日期';
                    break;
                case 'time':
                    self.form.type = 4;    // 时间
                    self.textType  = '时间';
                    break;
                case 'datetime':
                    self.form.type = 5;    // 日期时间
                    self.textType  = '日期时间';
                    break;
                case 'decimal':
                    self.form.type = 6;     // 小数
                    self.textType  = '小数';
                    break;
                case 'number':
                    self.form.type = 7;    // 整数
                    self.textType  = '整数';
                    break;
                case 'radio':
                    self.form.type = 8;    // 单选框
                    self.textType  = '单选框';
                    break;
                case 'checkbox':
                    self.form.type = 9;    // 多选框
                    self.textType  = '多选框';
                    break;
                case 'textarea':
                    self.form.type = 10;   // 多行文本
                    self.textType  = '多行文本  ';
                    break;
            }
            if(self.$route.query.com_type == 2 || self.$route.query.com_type == 8 || self.$route.query.com_type == 9){
                self.types = true;
            }
        },
        methods: {
            comeback() {
                let self = this;
                self.$router.push({path: '/formdesign',query: {id: self.$route.query.id}});
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
            upData(formClose){
                let self = this;
                this.$refs[formClose].validate((valid) => {
                    if(valid){
                        this.$axios.post('/makesCanton/Opera/Predefined/editAttrModify',qs.stringify({
                            key:self.$cookie.get('oKey'),
                            user_id:self.$cookie.get('token'),
                            form_id:self.$route.query.form_id,                  // 表单id
                            id:self.$route.query.id,                           // 组件id
                            com_type:self.form.com_type,                      // 组件类型
                            name:self.form.name,                             // 组件名
                            is_require:self.form.is_require,                // 必填
                            length:self.form.length,                     　// 数据长度
                            default_value: self.form.default_value,       // 默认值
                            accuracy: self.form.accuracy,                // 小数精度
                            validation: self.form.validation,                // 验证规则
                            predefined_value: self.form.predefined,       // 预定义内容
                            sort:self.form.sort,                      // 排序
                            remark:self.form.remark,                   // 字段描述
                        })).then( (res) =>{
                            if(res.data.status == 100){
                                self.$message({
                                    type: 'success',
                                    message: '操作完成'
                                });
                                self.open();
                            }else if(res.data.status == 102){
                                self.$message({
                                    type: 'success',
                                    message: res.data.msg
                                });
                            } else if(res.data.status == 1011){
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
            open() {
                let self = this;
                self.$router.push({path: '/designform',query: {form_id: self.$route.query.form_id}});
            },
            // 删除预定于内容
            removeDomain(item) {
                let self = this;
                let index = this.form.predefined.indexOf(item);
                if(this.form.predefined.length == 1){
                    self.$message({
                        type: 'success',
                        message: '至少填一个'
                    });
                }else {
                    this.form.domains.splice(index, 1)
                }
            },
            // 添加预定于内容
            addDomain() {
                this.form.predefined.push({
                    value: '',
                    key: Date.now()
                });
            },
            //获取验证规则
            getChannel(){
                let self = this;
                this.$axios.post('/makesCanton/ConstantConfig/getConstant',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    value:"form_validation_regulation",
                    type: "call"
                })).then( (res) =>{
                    if(res.data.status == 100){
                        self.rules = res.data.value.detail
                    }else if(res.data.status == 1011){
                        self.$message({
                            type: 'success',
                            message: '权限不足，请与管理员联系'
                        });
                    }else if(res.data.status == 1012){
                        self.$router.push('/login');
                    }
                })
            },



            //编辑
            getList() {
                let self = this;
                this.$axios.post('/makesCanton/Opera/Predefined/editAttrList', qs.stringify({
                    key: self.$cookie.get('oKey'),
                    user_id: self.$cookie.get('token'),
                    /* search: self.list.search,
                     num: self.list.num,
                     page: self.list.page,*/
                    form_id: self.$route.query.form_id,
                    id: self.$route.query.id
                })).then((res) => {
                    self.listLoading = false;
                    if (res.data.status == 100) {
                        self.dataList = res.data.value[0];
                        self.form = res.data.value[0];
                        /*self.tableData = res.data.value;
                         self.olderList = self.tableData.map(v => v.id);
                         self.newList = self.olderList.slice();
                         self.$nextTick(() => {
                         this.setSort()
                         });

                         self.page = res.data.page;*/
                          self.form.length = Number(self.form.length);
                    } else if (res.data.status == 1011) {
                        self.$message({
                            type: 'success',
                            message: '权限不足，请与管理员联系'
                        });
                    } else if (res.data.status == 1012) {
                        self.$router.push('/login');
                    }
                })
            },
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
        font-size: 25px;
        cursor: pointer;
    }
</style>
