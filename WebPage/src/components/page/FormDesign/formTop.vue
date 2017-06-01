<template>
    <div class="">
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span style="line-height: 36px;">表单注册</span>
                <el-button style="float: right;" type="primary" v-show="editInfo" @click="editData">编辑</el-button>
                <el-button style="float: right;" type="primary" v-show="!editInfo" @click="submitForm('ruleForm2')">提交</el-button>
                <el-button style="float: right;" v-show="!editInfo" @click="editData">取消</el-button>
            </div>
            <el-form :model="ruleForm2" ref="ruleForm2" label-width="100px" class="demo-ruleForm">
                <el-form-item label="表单名称"  prop="name"
                              :rules="[{ required: true, message: '请输入表单名称', trigger: 'blur，change' }]">
                    <div v-show="editInfo">{{dataList.name}}</div>
                    <el-input v-show="!editInfo" type="text"
                              v-model="ruleForm2.name" auto-complete="off"
                              style="width: 30%;"></el-input>
                </el-form-item>

                <el-form-item label="可用状态" >
                    <div v-show="editInfo">{{dataList.enabled_value}}</div>
                    <el-radio v-show="!editInfo" class="radio" v-model="ruleForm2.enabled" label="1" >启用</el-radio>
                    <el-radio v-show="!editInfo" class="radio" v-model="ruleForm2.enabled" label="0" >停用</el-radio>
                </el-form-item>

                <el-form-item label="表单说明" prop="remark" style="min-height: 130px;"
                              :rules="[{ required: true, message: '域名不能为空', trigger: 'blur'}]">
                    <div v-show="editInfo">{{dataList.remark}}</div>
                    <el-input v-show="!editInfo" type="textarea" :rows="5" placeholder="请输入表单说明"
                              v-model="ruleForm2.remark" style="width: 30%;"></el-input>
                    <!-- <el-input  type="text" v-model="ruleForm2.info" auto-complete="off" ></el-input>-->
                </el-form-item>
            </el-form>

        </el-card>
    </div>
</template>
<script>
    export default{
        data () {
            return{

            }
        },
        methods:{
            submitForm(formName) {
                let self = this;
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.$axios.post('/makesCanton/Opera/Predefined/editModify', qs.stringify({
                            key: self.$cookie.get('oKey'),
                            user_id: self.$cookie.get('token'),
                            id: self.$route.query.id,
                            name: self.ruleForm2.name,
                            remark: self.ruleForm2.remark,
                            enabled: self.ruleForm2.enabled
                        })).then((res) => {
                            if (res.data.status == 100) {
                                self.tableData = res.data.value;
                                self.$message({
                                    type: 'success',
                                    message: "修改成功"
                                });
                                self.editData();
                                self.getList();
                            } else if (res.data.status == 1011) {
                                self.$message({
                                    type: 'success',
                                    message: '权限不足，请与管理员联系'
                                });
                            } else if (res.data.status == 1012) {
                                self.$router.push('/login');
                            }
                        })
                    }
                });
            },
        }
    }
</script>
<style>


</style>

