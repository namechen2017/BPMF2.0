<template>
    <div class="details">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-date"></i> 工单</el-breadcrumb-item>
                <el-breadcrumb-item>工单详情</el-breadcrumb-item>
            </el-breadcrumb>
            <div class="btns">
                <el-button type="" @click="comeback">返回</el-button>
                <el-button type="info" @click="assignments">分配</el-button>
                <el-button type="danger" @click="deleteData">删除</el-button>
                <el-button type="primary" @click="evaluationButton">评价</el-button>
                <el-button type="success" @click="closeButton">关闭</el-button>
            </div>
            <el-row :gutter="20">
                <el-col :span="12"><div class="grid-content bg-purple">
                    <el-card class="box-card">
                        <div slot="header">
                            <el-row>
                                <el-col :span="18">
                                    <el-form label-width="50px" class="demo-ruleForm">
                                        <el-form-item label="主题" prop="title">
                                            <div v-show="editDataes" class="info-data">{{orderFormInfo.title}}</div>
                                            <el-input v-show="!editDataes" v-model="orderFormEdit.title" placeholder="请输入主题说明"></el-input>
                                        </el-form-item>
                                    </el-form>
                                </el-col>
                                <el-col :span="6" class="text-right">
                                    <el-button style="float: right;" type="primary" @click="editDatas('orderFormEdit',orderFormEdit.id)" v-show="!editDataes">确 定</el-button>
                                    <el-button style="float: right;margin-right: 15px" @click="editDataes=!editDataes" v-show="!editDataes">取 消</el-button>
                                    <el-button style="float: right;" type="primary" @click="editButton('a')" v-show="editDataes">编辑</el-button>
                                </el-col>
                            </el-row>
                        </div>
                        <div class="text item">
                            <div v-show="editDataes">{{orderFormInfo.content}}</div>
                            <el-input v-show="!editDataes" type="textarea" :rows="5" placeholder="请输入内容"
                                      v-model="orderFormEdit.content"></el-input>
                        </div>
                    </el-card>
                    <el-card class="box-card info-dynamic">
                        <div slot="header" class="clearfix">
                            <span style="line-height: 36px;">处理</span>
                            <el-button style="float: right;" type="primary">处理</el-button>
                        </div>
                        <div v-for="o in 4" :key="o" class="text item">
                            {{'列表内容 ' + o }}
                        </div>
                    </el-card>
                    <el-card class="box-card info-dynamic">
                        <div slot="header" class="clearfix">
                            <span style="line-height: 36px;">更新动态</span>
                        </div>
                        <div class="text item">
                            <div>{{}}</div>
                            <div></div>
                        </div>
                        <div class="text item"></div>
                    </el-card>

                </div>
                </el-col>
                <el-col :span="12"><div class="grid-content bg-purple">
                    <el-card class="box-card">
                        <div slot="header" class="clearfix">
                            <span style="line-height: 36px;">属性</span>
                            <el-button style="float: right;" type="primary" @click="editDatas('orderFormEdit',orderFormEdit.id)" v-show="!editData">确 定</el-button>
                            <el-button style="float: right;margin-right: 15px" @click="editData=!editData" v-show="!editData">取 消</el-button>
                            <el-button style="float: right;" type="primary" @click="editButton('b')" v-show="editData">编辑</el-button>
                        </div>
                        <div  class="text item style_form">
                            <el-form :model="orderFormEdit" :rules="rulesEdit" ref="orderFormEdit" label-width="150px" class="demo-ruleForm">
                                <el-form-item label="编号" >
                                    <div class="info-data">{{orderFormInfo.no}}</div>
                                </el-form-item>
                                <el-form-item label="销售渠道" prop="channel_value">
                                    <div v-show="editData" class="info-data">{{orderFormInfo.channel_value}}</div>
                                    <el-select v-show="!editData" v-model="orderFormEdit.channel"
                                               placeholder="请选择销售渠道">
                                        <el-option
                                            v-for="item in channelList"
                                            :label="item.key_value"
                                            :value="item.key_name">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                                <el-form-item label="产品类型" prop="type_value">
                                    <div v-show="editData" class="info-data">{{orderFormInfo.type_value}}</div>
                                    <el-select v-show="!editData" v-model="orderFormEdit.type" placeholder="请选择产品类型">
                                        <el-option
                                            v-for="item in productList"
                                            :label="item.key_value"
                                            :value="item.key_name" :selected="item.key_name == orderFormEdit.type">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                                <el-form-item label="客户名称" prop="customer_value">
                                    <!-- <el-input v-model="orderForm.clientName" placeholder="客户名称" >  </el-input>-->
                                    <div v-show="editData" class="info-data">{{orderFormInfo.customer_value}}</div>
                                    <el-autocomplete v-show="!editData" v-model="orderFormEdit.customer_value"
                                                     :fetch-suggestions="querySearchAsync"
                                                     placeholder="请输入客户名称"
                                                     @select="handleSelectInfo"
                                    ></el-autocomplete>
                                </el-form-item>
                                <el-form-item label="合同订单编号" prop="no">
                                    <div v-show="editData" class="info-data">{{orderFormInfo.contract_no}}</div>
                                    <el-input v-show="!editData" v-model="orderFormEdit.contract_no" placeholder="合同订单编号"></el-input>
                                </el-form-item>
                                <el-form-item label="优先级" prop="priority_value">
                                    <div v-show="editData" class="info-data">{{orderFormInfo.priority_value}}</div>
                                    <el-select v-show="!editData" v-model="orderFormEdit.priority" placeholder="请输入优先级">
                                        <el-option
                                            v-for="item in priorityList"
                                            :key="item.value"
                                            :label="item.key_value"
                                            :value="item.key_name" :selected="item.key_name == orderFormEdit.priority">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                               <!-- <el-form-item label="主题" prop="title">
                                    <div v-show="editData" class="info-data">{{orderFormEdit.title}}</div>
                                    <el-input v-show="!editData" v-model="orderFormEdit.title" placeholder="请输入主题说明"></el-input>
                                </el-form-item>
                                <el-form-item label="任务说明" prop="content"  >
                                    <div v-show="editData" class="info-data">{{orderFormEdit.content}}</div>
                                    <el-input v-show="!editData" type="textarea" :rows="5" placeholder="请输入内容" v-model="orderFormEdit.content"></el-input>
                                </el-form-item>-->
                                <el-form-item label="责任客服人员" class="border-t">
                                    <div v-show="editData" class="info-data">xxx</div>
                                </el-form-item>
                                <el-form-item label="责任售后人员" class="border-b">
                                    <div v-show="editData" class="info-data">xxx</div>
                                </el-form-item>
                                <el-form-item label="创建时间" >
                                    <div v-show="editData" class="info-data">{{orderFormInfo.create_time}}</div>
                                </el-form-item>
                                <el-form-item label="分配时间" >
                                    <div v-show="editData" class="info-data"></div>
                                </el-form-item>
                            </el-form>
                        </div>
                    </el-card>
                </div></el-col>
            </el-row>
        </div>
        <el-dialog title="分配任务工作单" :visible.sync="distribution">
                <el-form :model="form" :rules="rulesForm" ref="form" label-width="150px" class="demo-ruleForm">
                    <el-form-item label="接收人" prop="name" >
                        <el-autocomplete  v-model="form.name"
                                         :fetch-suggestions="querySearchAsyncUser"
                                         placeholder="请输入客户名称"
                                         @select="handleSelectInfoUser"
                        ></el-autocomplete>
                    </el-form-item>
                    <el-form-item label="要求完成日期" prop="date">
                        <el-date-picker
                            v-model="form.date"
                            type="date"
                            placeholder="选择日期"
                            :picker-options="pickerOptions0">
                        </el-date-picker>
                    </el-form-item>
                    <el-form-item label="备注" class="text-right" >
                        <el-input  type="textarea" :rows="5" placeholder="请输入内容"
                                  v-model="form.remark"></el-input>
                    </el-form-item>
                </el-form>
                <div slot="footer" class="dialog-footer">
                    <el-button @click="assignments">取 消</el-button>
                    <el-button type="primary" @click="upData('form')">确 定</el-button>
                </div>
        </el-dialog>
        <el-dialog title="关闭任务工作单" :visible.sync="closeTest">
            <el-form :model="formClose" :rules="rulesFormClose" ref="formClose" label-width="150px" class="demo-ruleForm">

                <el-form-item label="关闭原因" class="text-right" prop="content">
                    <el-input  type="textarea" :rows="5" placeholder="请输入关闭该任务工作单的原因"
                               v-model="formClose.content"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="closeButton">取 消</el-button>
                <el-button type="primary" @click="upDataClose('formClose')">确 定</el-button>
            </div>
        </el-dialog>
        <el-dialog title="评价任务工作单" :visible.sync="evaluation">
            <el-form :model="formEvaluation"  :rules="rulesEvaluation" ref="formEvaluation" label-width="150px" class="demo-ruleForm">
                <el-form-item label="响应速度" prop="speed">
                    <el-rate v-model="formEvaluation.speed"></el-rate>
                </el-form-item>
                <el-form-item label="完成时间" prop="end_time">
                    <el-rate v-model="formEvaluation.end_time"></el-rate>
                </el-form-item>
                <el-form-item label="工作质量" prop="quality">
                    <el-rate v-model="formEvaluation.quality"></el-rate>
                </el-form-item>
                <el-form-item label="关闭原因" class="text-right" prop="comment">
                    <el-input  type="textarea" :rows="5" placeholder="请输入简单的评价下"
                               v-model="formEvaluation.comment"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="evaluationButton">取 消</el-button>
                <el-button type="primary" @click="evaluationData('formEvaluation')">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>
<script>
    import qs from 'qs'
    export default {
        data () {
            return {
                /*orderFormInfo:{          //  编辑任务单
                    channel:'',          //  销售渠道id
                    channel_value:'',    //  销售渠道
                    type:'',             //  产品类型
                    type_value:'',       //  产品类型
                    customer_id:'',      //  客户名称id
                    customer_value:'',   //  客户名称
                    clientId:'',         //  客户名称id
                    contract_no:'',      //  合同订单编号
                    priority:'',         //  优先级id
                    priority_value:'',   //  优先级
                    title:'',            //  主题
                    content:'',          //  任务说明
                    status:1,            //
                    no:''                //  编号

                }*/
                orderFormInfo:[],
                orderFormEdit:{          //  编辑任务单
                    channel:'',          //  销售渠道id
                    channel_value:'',    //  销售渠道
                    type:'',             //  产品类型id
                    type_value:'',       //  产品类型
                    customer_id:'',      //  客户名称id
                    customer_value:'',   //  客户名称
                    clientId:'',         //  客户名称id
                    contract_no:'',      //  合同订单编号
                    priority:'',         //  优先级id
                    priority_value:'',   //  优先级
                    title:'',            //  主题
                    content:'',          //  任务说明
                    status:1,            //
                    no:'',               //  编号
                    id:''
                },
                rulesEdit:{
                    channel_value: [
                        {required: true, message: '请输入销售渠道', trigger: 'change'}
                    ],
                    type_value:[
                        {required: true, message: '请输入产品类型', trigger: 'change'}
                    ],
                    customer_value:[
                        {required: true, message: '请输入客户名称', trigger: 'blur'}
                    ],
                    no:[
                        {required: true, message: '请输入合同订单编号', trigger: 'blur'}
                    ],
                    priority_value:[
                        {required: true, message: '请输入优先级', trigger: 'change'}
                    ],
                    title:[
                        {required: true, message: '请输入主题', trigger: 'blur'}
                    ],
                    content:[
                        {required: true, message: '请输入任务说明', trigger: 'blur'}
                    ]
                },
                editData:true,              //   修改
                editDataes:true,              //   修改
                //  远程搜索
                restaurants: [],             //  搜索的列表
                timeout:  null,
                channelList:[],              //  销售类型
                productList:[],              //  产品类型
                priorityList:[],             //  优先级
                distribution:false,          //  分配
                form:{
                    name:'',                  //  分配人
                    remark:'',                //  备注
                    id:'',                    //  分配人id
                    date:''                   //  要求完成日期
                },
                rulesForm:{
                    name:[
                        {required: true, message: '请选择您要分配的人员', trigger: 'change'}
                    ],
                    date:[
                        {type:'date',required: true, message: '必须选择一个要求完成日期', trigger: 'change'}
                    ]
                },
                pickerOptions0: {              // 选择的时间为从今天开始
                    disabledDate(time) {
                        return time.getTime() < Date.now() - 8.64e7;
                    }
                },
                restaurantsUser:[],         //  用户搜索
                timeoutA:null,
                closeTest: false,           //  关闭任务
                formClose: {
                    content:''              //  关闭理由
                },
                rulesFormClose:{
                    content:[
                        {required: true, message: '请填写您要关闭的原因', trigger: 'blur'},
                        {max: 100,message:'长度不超过100个字符',trigger:'blur'}
                    ]
                },
                evaluation:false,         //  评价
                formEvaluation:{
                    speed:'',            //  相应速度
                    end_time:'',         //  完成时间
                    quality:'',          //  工作质量
                    comment:''           //  评价
                },
                rulesEvaluation:{
                    speed:[
                        {required: true, message: '请评价响应速度', trigger: 'blur'},
                    ],
                    end_time:[
                        {required: true, message: '请评价完成时间', trigger: 'blur'},
                    ],
                    quality:[
                        {required: true, message: '请评价工作质量', trigger: 'blur'},
                    ],
                    comment:[
                        {required: true, message: '请评价响应速度', trigger: 'blur'},
                    ]
                }
            }
        }, created() {
            this.getList();
            this.getChannel();
            this.getProduct();
            this.getPriority();
        },
        methods: {
            //销售渠道
            getChannel(){
                let self = this;
                this.$axios.post('/makesCanton/ConstantConfig/getConstant',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    value:"work_channel",
                    type:"call"
                })).then( (res) =>{
                    console.log(res);
                    if(res.data.status == 100){
                        self.channelList=res.data.value.detail
                    }else if(res.data.status == 1012){
                        self.$router.push('/login');
                    }
                })
            },
            //产品类型
            getProduct(){
                let self = this;
                this.$axios.post('/makesCanton/ConstantConfig/getConstant',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    value:"work_type",
                    type: "call"
                })).then( (res) =>{
                    if(res.data.status == 100){
                        self.productList=res.data.value.detail
                    }else if(res.data.status == 1012){
                        self.$router.push('/login');
                    }
                })
            },
            //优先级
            getPriority(){
                let self = this;
                this.$axios.post('/makesCanton/ConstantConfig/getConstant',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    value:"work_priority",
                    type: "call"
                })).then( (res) =>{
                    if(res.data.status == 100){
                        self.priorityList=res.data.value.detail
                    }else if(res.data.status == 1012){
                        self.$router.push('/login');
                    }
                })
            },

            getList(){
                let self = this;
                this.$axios.post('/makesCanton/Opera/WorkTask/workList',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    num:1,
                    page:1,
                    id:self.$route.query.id
                })).then( (res) =>{
                    if(res.data.status == 100){
                        self.orderFormInfo = res.data.value[0];
                       /* self.orderFormEdit = res.data.value[0];*/
                      /*  self.formInlineEdit.serial = Number(self.formInlineEdit.serial);*/
                    }else if(res.data.status == 1012){
                        self.$router.push('/login');
                    }

                })
            },

            // 远程模糊搜索列表-客户名称
            loadAll(){
                let self = this;
                this.$axios.post('/makesCanton/ApplyVps/getCustom',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    name:self.orderFormEdit.clientName,
                })).then( (res) =>{
                    if(res.data.status == 100){
                        self.restaurants = res.data.value;
                    }else if(res.data.status == 1012){
                        self.$router.push('/login');
                    }
                })
            },
            // 远程模糊搜索
            querySearchAsync(queryString, cb) {
                let self = this;
                let restaurants = self.restaurants;
                let results = queryString ? restaurants.filter(self.createStateFilter(queryString)) : restaurants;

                clearTimeout(self.timeout);
                this.timeout = setTimeout(() => {
                    cb(results);
                }, 3000 * Math.random());
            },
            createStateFilter(queryString) {
                return (state) => {
                    return (state.value.indexOf(queryString.toLowerCase()) === 0);
                };
            },
            handleSelect(item) {
                let self = this;
                self.orderFormEdit.clientId = item.id
            },
            // 远程模糊搜索选取
            handleSelectInfo(item) {
                let self = this;
                self.orderFormEdit.customer_id = item.key
            },

            // 远程模糊搜索列表-用户
            loadAllUser(){
                let self = this;
                this.$axios.post('/makesCanton/get/user',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    search:self.form.name,
                })).then( (res) =>{
                    if(res.data.status == 100){
                        self.restaurantsUser = res.data.value;
                        self.restaurantsUser.forEach(function(item){
                            // element ui远程搜索显示的列表的的键值固定为value了，因此需要将我们需要的数据赋值给value
                            //先获得要修改键的值，避免丢失。
                            let temp=item.real_name;
                            /* delete(item.real_name);*/
                            //再定义新的键和值
                            item.value=temp;
                        })
                    }else if(res.data.status == 1012){
                        self.$router.push('/login');
                    }
                })
            },
            // 远程模糊搜索
            querySearchAsyncUser(queryString, cb) {
                let self = this;
                let restaurants = self.restaurantsUser;
                let results = queryString ? restaurants.filter(self.createStateFilter(queryString)) : restaurants;
                clearTimeout(self.timeoutA);
                this.timeoutA = setTimeout(() => {
                    cb(results);
                }, 3000 * Math.random());
            },
            // 远程模糊搜索选取
            handleSelectInfoUser(item) {
                let self = this;
                self.form.id = item.id

            },
            createStateFilter(queryString) {
                return (state) => {
                    return (state.value.indexOf(queryString.toLowerCase()) === 0);
                };
            },


            //修改按钮
            editButton(val) {
                let self = this;
                if(val == 'a'){
                    self.editDataes = !self.editDataes;
                }else if(val == 'b'){
                    self.editData = !self.editData;
                }
                this.$axios.post('/makesCanton/Opera/WorkTask/workList',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    num:1,
                    page:1,
                    id:self.$route.query.id
                })).then( (res) =>{
                    if(res.data.status == 100){
                       /* self.orderFormInfo = res.data.value[0];*/
                         self.orderFormEdit = res.data.value[0];
                        /*  self.formInlineEdit.serial = Number(self.formInlineEdit.serial);*/
                    }else if(res.data.status == 1012){
                        self.$router.push('/login');
                    }

                })
            },
            //修改数据
            editDatas(formInlineEdit,id) {
                /* Opera/Number/editNumberRule*/
                let self = this;
                this.$refs[formInlineEdit].validate((valid) => {
                    if(valid) {
                        this.$axios.post('/makesCanton/Opera/WorkTask/workEdit', qs.stringify({
                            key: self.$cookie.get('oKey'),
                            user_id: self.$cookie.get('token'),
                            id:id,
                            channel: self.orderFormEdit.channel,
                            type: self.orderFormEdit.type,
                            customer_id: self.orderFormEdit.customer_id,
                            priority: self.orderFormEdit.priority,
                            contract_no: self.orderFormEdit.contract_no,
                            title: self.orderFormEdit.title,
                            content: self.orderFormEdit.content,
                            status: self.orderFormEdit.status
                        })).then((res) => {
                            self.editDataes = true;
                            self.editData = true;
                            if (res.data.status == 100) {
                                self.getList();
                                self.dialogFormVisible = false;
                                this.$message({
                                    message: "编辑成功",
                                    type: 'success'
                                });
                            }else if(res.data.status == 102){
                                this.$message({
                                    message: res.data.msg,
                                    type: 'success'
                                });
                            }else  if (res.data.status == 1012) {
                                self.$router.push('/login');
                            }else{
                                this.$message({
                                    message: res.data.msg,
                                    type: 'success'
                                });
                            }

                        })
                    }
                })
            },
            //返回列表首页
            comeback() {
                let self = this;
                self.$router.push('/workorderlists');
            },
            //删除按钮
            deleteData() {
                let self = this;
                this.$confirm('是否删除该工单任务？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.$axios.post('/makesCanton/Opera/WorkTask/workClose',qs.stringify({
                        key:self.$cookie.get('oKey'),
                        user_id:self.$cookie.get('token'),
                        id:self.$route.query.id})).then( (res) =>{
                        if(res.data.status == 100){
                            self.$message({
                                type: 'success',
                                message: '删除成功!'
                            });
                            setTimeout(function () {
                                self.$router.push('/workorderlists');
                            },1000)
                        }else if(res.data.status == 1012){
                            self.$router.push('/login');
                        }else{
                            self.$message({
                                type: 'success',
                                message: res.data.msg
                            })
                        }


                    })

                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });
            },
            // 分配按钮
            assignments() {
                let self = this;
                self.distribution = !self.distribution;
            },
            // 分配任务
            upData(rulesForm) {
                /*/Opera/WorkTask/workEdit*/
                let self = this;
                console.log(self.form);
                this.$refs[rulesForm].validate((valid) => {
                    if(valid){
                        this.$axios.post('/makesCanton/Opera/WorkTask/workEdit',qs.stringify({
                            key:self.$cookie.get('oKey'),
                            user_id:self.$cookie.get('token'),
                            id:self.$route.query.id,
                            receive_id:self.form.id,             // 被分配人的id
                            assign_remark:self.form.remark,          // 分配备注
                            target_time: self.form.date              // 要求完成时间
                        })).then( (res) => {
                            console.log(res);
                            if (res.data.status == 100) {
                            } else if (res.data.status == 1012) {
                                self.$router.push('/login');
                            }
                        })
                    }
                })
            },
            // 关闭按钮
            closeButton() {
                this.closeTest = !this.closeTest;
            },
            upDataClose(formClose){
                let self = this;
                console.log(self.form);
                this.$refs[formClose].validate((valid) => {
                    if(valid){
                        this.$axios.post('/makesCanton/Opera/WorkTask/workEdit',qs.stringify({
                            key:self.$cookie.get('oKey'),
                            user_id:self.$cookie.get('token'),
                            id:self.$route.query.id,
                            remark:self.formClose.content           // 要求完成时间
                        })).then( (res) => {
                            self.formClose = {
                                content:''
                            };
                            console.log(res);
                            if (res.data.status == 100) {
                            } else if (res.data.status == 1012) {
                                self.$router.push('/login');
                            }
                        })
                    }
                })
            },
            evaluationButton (){
                let self = this;
                this.evaluation = !self.evaluation
            },
            evaluationData(formEvaluation) {
                let self = this;
                console.log(self.form);
                this.$refs[formEvaluation].validate((valid) => {
                    if(valid){
                        this.$axios.post('/makesCanton/Opera/WorkTask/workEdit',qs.stringify({
                            key:self.$cookie.get('oKey'),
                            user_id:self.$cookie.get('token'),
                            id:self.$route.query.id,
                            end_time:self.formEvaluation.end_time,
                            speed_star:self.formEvaluation.speed,
                            quality_star:self.formEvaluation.quality,
                            comment:self.formEvaluation.coument           // 要求完成时间
                        })).then( (res) => {
                            console.log(res);
                            self.formEvaluation = {
                                comment:'',
                                speed:'',
                                quality:'',
                                end_time:''
                            };
                            console.log(res);
                            if (res.data.status == 100) {
                            } else if (res.data.status == 1012) {
                                self.$router.push('/login');
                            }
                        })
                    }
                });
                console.log(this.formEvaluation)
            }

        },
        mounted() {
            let self = this;
            self.restaurants = self.loadAll();
            self.restaurantsUser = self.loadAllUser();
        }
    }
</script>
<style>
    .el-row {
        padding: 10px 0;
    }
    .el-autocomplete{
        width: 100%;
    }
    .el-form-item__content .info{
        padding-left: 100px;
    }
    .border-b{
        border-bottom:1px solid #000;
    }
    .border-t{
        border-top:1px solid #000;
    }
    .style_form .el-form-item__label{
        text-align: left;
    }
    .el-form-item{
        margin-bottom: 10px;
    }
    .clearfix:before,
    .clearfix:after {
        display: table;
        content: "";
    }
    .clearfix:after {
        clear: both
    }
    .info-dynamic{
       margin-top: 30px;
    }
    .btns{
        margin: 15px 0;
    }
</style>

