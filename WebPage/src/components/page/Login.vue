<template>
    <div class="login-wrap">
        <div class="ms-title">后台管理系统</div>
        <div class="ms-login">
            <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="0px" class="demo-ruleForm">
                <el-form-item prop="username">
                    <el-input v-model="ruleForm.username" placeholder="username"></el-input>
                </el-form-item>
                <el-form-item prop="password">
                    <el-input type="password" placeholder="password" v-model="ruleForm.password" @keyup.enter.native="submitForm('ruleForm')"></el-input>
                </el-form-item>
                <div class="login-btn">
                    <el-button type="primary" @click="submitForm('ruleForm')">登录</el-button>
                </div>
                <p style="font-size:12px;line-height:30px;color:#999;">Tips : 请确认用户名和密码。</p>
            </el-form>
        </div>
    </div>
</template>

<script>
    import qs from 'qs'
    import { message } from 'element-ui';
    export default {
        data: function(){
            return {
                ruleForm: {
                    username: '',
                    password: ''
                },
                rules: {
                    username: [
                        { required: true, message: '请输入用户名', trigger: 'blur,change' }
                    ],
                    password: [
                        { required: true, message: '请输入密码', trigger: 'blur,change' }
                    ]
                }
            }
        },
        methods: {
            submitForm(formName) {
                const self = this;
                self.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.$axios.post('/makesCanton/login',qs.stringify({
                            username:self.ruleForm.username,
                            password:self.ruleForm.password
                        })).then(function (res)  {
                            console.log(res);
                            self.$message({
                                type: 'success',
                                message: res.data.msg
                            });
                            if(res.data.status == 100){
                                self.$cookie.set('oKey',res.data.value.key);
                                self.$cookie.set('token',res.data.value.user_id);
                                self.$cookie.set('username',res.data.value.user.username);
                                self.$message({
                                    type: 'success',
                                    message: '登录成功'
                                });
                                setTimeout(function () {
                                    self.$router.push('/readme');
                                },1000)
                            }else if(res.data.status == 101){

                            }else if(res.data.status == 103){

                            }else if(res.data.status == 104){
                                console.log(res.data.msg);
                            }else if(res.data.status == 1011 ){
                                self.$message({
                                    type: 'success',
                                    message: '权限不足，请与管理员联系'
                                });
                            }
                        }).catch(function (response) {
                        });

                       /* localStorage.setItem('ms_username',self.ruleForm.username);*/
                       //console.log(self.ruleForm.username);
                      // console.log(self.ruleForm.password);
                       // this.$axios.post('/api/login',{
                        //    username:"admin",
                        //    password:'415263'
                       // }).then((res) => {
                        //    console.log(res);
                           /* if(res.data.status == 1012){
                                self.$router.push('/login');
                            }*/

                      //  })
                        /*self.$router.push('/readme');*/
                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            }
        }
    }
</script>

<style scoped>
    .login-wrap{
        position: relative;
        width:100%;
        height:100%;
    }
    .ms-title{
        position: absolute;
        top:50%;
        width:100%;
        margin-top: -230px;
        text-align: center;
        font-size:30px;
        color: #fff;

    }
    .ms-login{
        position: absolute;
        left:50%;
        top:50%;
        width:300px;
        height:160px;
        margin:-150px 0 0 -190px;
        padding:40px;
        border-radius: 5px;
        background: #fff;
    }
    .login-btn{
        text-align: center;
    }
    .login-btn button{
        width:100%;
        height:36px;
    }
</style>
