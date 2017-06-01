<template>
    <el-row>
       <el-col :span="24">
           <div class="title">
               <!--<i class="el-icon-arrow-left" @click="">返回</i>-->
               <el-button  @click="comeback">返回</el-button>
           </div>
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
           <el-card class="box-card" style="margin-top: 20px;">
               <div slot="header" class="clearfix">
                   <span style="line-height: 36px;">表单注册</span>
                   <el-button style="float: right;" size="small" type="primary"
                              @click="changeList()">调整优先级</el-button>
                   <el-button style="float: right;margin-right: 15px;" size="small" type="success"
                              @click="add()">添加新字段</el-button>
               </div>
                <el-table :data="tableData" v-loading.body="listLoading" border fit highlight-current-row style="width: 100%">

                   <el-table-column type="index" align="center" label="序号" width="65">

                   </el-table-column>

                   <el-table-column align="center" label="字段名称" width="250">
                       <template scope="scope">
                           <span>{{scope.row.name}}</span>
                       </template>
                   </el-table-column>

                   <el-table-column align="center" label="字段类型" width="200">
                       <template scope="scope">
                           <span>{{scope.row.com_value}}</span>
                       </template>
                   </el-table-column>

                    <el-table-column align="center" label="字段说明" >
                        <template scope="scope">
                            <span>{{scope.row.remark}}</span>
                        </template>
                    </el-table-column>

                   <el-table-column align="center" label="操作" width="300">
                       <template scope="scope">
                           <el-button size="small" type="info"
                                      @click="edit(scope.row.id,scope.row.com_type)">编辑</el-button>
                           <el-button size="small" type="danger"
                                      @click="deleteItem(scope.row.id)">删除</el-button>

                       </template>
                   </el-table-column>

                </el-table>
            </el-card>
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
               tableData:[],
                listLoading: true,
                sortable: null,
                olderList: [],
                newList: [],
                page:[],
                list:{              // 列表信息
                    search:'',        // 搜索
                    num:10,         // 显示数量
                    page:1         // 显示页
                },
                //表单信息
                ruleForm2:{
                    enabled_value:'',
                    enaled:'',
                    name:'',
                    remark:'',
                    id:''
                },
                rules2:{
                    name:[
                        {required: true, message: '请输入表单名称', trigger: 'change,blur'}
                    ],
                    info:[
                        {required: true, message: '请输入表单名称', trigger: 'change,blur'}
                    ],
                    status:[
                        {required: true, message: '请输入表单说明', trigger: 'blur'}
                    ]
                },
                editInfo:true,
                dataList:[],  //展示表单信息
                visible2:false,
                ids:'',
                idz:''

            }
        },
        created(){
            this.getList();
            this.formInfo();
        },
        methods: {
            // 获取form信息
            formInfo() {
                let self = this;
                this.$axios.post('/makesCanton/Opera/Predefined/formList', qs.stringify({
                    key: self.$cookie.get('oKey'),
                    user_id: self.$cookie.get('token'),
                    search: self.list.search,
                    num: 1,
                    page: 1,
                    id:self.$route.query.id
                })).then((res) => {
                   if (res.data.status == 100) {
                        self.dataList = res.data.value[0];
                        self.ruleForm2 = res.data.value[0];
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
            getList() {
                let self = this;
                this.$axios.post('/makesCanton/Opera/Predefined/editAttrList', qs.stringify({
                    key: self.$cookie.get('oKey'),
                    user_id: self.$cookie.get('token'),
                    search: self.list.search,
                    num: self.list.num,
                    page: self.list.page,
                    form_id: self.$route.query.form_id
                })).then((res) => {
                    self.listLoading = false;
                    if (res.data.status == 100) {
                        self.tableData = res.data.value;
                        self.olderList = self.tableData.map(v => v.id);
                        self.newList = self.olderList.slice();
                        self.$nextTick(() => {
                            this.setSort()
                        });
                        self.page = res.data.page;
                      /*  self.page.count = Number(self.page.count);*/
                    }else if(res.data.status == 102){

                    }else if (res.data.status == 1011) {
                        self.$message({
                            type: 'success',
                            message: '权限不足，请与管理员联系'
                        });
                    } else if (res.data.status == 1012) {
                        self.$router.push('/login');
                    }
                })
            },
            setSort() {
                const el = document.querySelectorAll('.el-table__body-wrapper > table > tbody')[0];
                this.sortable = Sortable.create(el, {
                    // handle: '.drag-handler',
                    onEnd: evt => {
                        const tempIndex = this.newList.splice(evt.oldIndex, 1)[0];
                        this.newList.splice(evt.newIndex, 0, tempIndex);
                    }
                });
            },
            add() {
                let self = this;
                self.$router.push({ path: '/formdesign',query: { form_id: self.$route.query.form_id}})
            },
            edit(id,type){
                let self = this;
               /* this.$router.push({path: 'forminfo', query: { id: id,form_id:self.$route.query.form_id}})*/
                self.$router.push({ path: 'editform',query: { id: id,form_id:self.$route.query.form_id,com_type:type}})
            },
            //调整优先级
            changeList() {
              /*  Opera/Predefined/editSort*/
              let self = this;
                self.newList.forEach(function (value,index) {
                    self.newList[index]=Number(value)
                    self.ids = self.newList.join(',')
                });
                self.olderList.forEach(function (value,index) {
                    self.olderList[index]=Number(value);
                    self.idz = self.olderList.join(',')
                });
                if(self.ids==self.idz){
                    self.$message({
                        type: 'success',
                        message: '没有可以调整顺序的地方'
                    });
                }else{
                    this.$axios.post('/makesCanton/Opera/Predefined/editSort', qs.stringify({
                        key: self.$cookie.get('oKey'),
                        user_id: self.$cookie.get('token'),
                        ids: self.ids
                    })).then((res) => {
                        if (res.data.status == 100) {
                            self.$message({
                                type: 'success',
                                message: '调整成功'
                            });
                            /* self.$nextTick(() => {
                             location.reload(true);
                             });*/
                            setTimeout(function () {
                                self.tableData = [];
                                self.getList();
                            },1000);
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
            },
            comeback() {
                let self = this;
                /*self.$router.push('/designform');*/
            },
            submitForm(formName) {
                let self = this;
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.$axios.post('/makesCanton/Opera/Predefined/editModify', qs.stringify({
                            key: self.$cookie.get('oKey'),
                            user_id: self.$cookie.get('token'),
                            id: self.$route.query.form_id,
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
            editData() {
                let self = this;
                self.editInfo = !self.editInfo;
            },
            comeback() {
                let self = this;
                self.$router.push('/homeform');
            },
            deleteItem(id) {
                /*Opera/Predefined/componentClose*/
                let self = this;
                self.$confirm('此操作将永久删除该文件, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.$axios.post('/makesCanton/Opera/Predefined/editAttrSwitch', qs.stringify({
                        key: self.$cookie.get('oKey'),
                        user_id: self.$cookie.get('token'),
                        id: id,
                    })).then((res) => {
                        if (res.data.status == 100) {
                            self.$message({
                                type: 'success',
                                message: '删除成功'
                            });
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
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });


            }
        }
    }
</script>

<style scoped>
    /*.forms{
        min-height: 80vh;
        border: 1px solid #cccccc;
    }
    .form-title{
        padding: 10px 5px;
    }
    .form-title:hover{
        cursor: pointer;
    }*/
    .title{
        padding: 15px 10px;
        color: #00B38B;
        border-bottom: 1px solid #ccc   ;
    }
    .title-tip{
        padding: 15px 10px;
        background-color: #F6F8F8;
    }

</style>
