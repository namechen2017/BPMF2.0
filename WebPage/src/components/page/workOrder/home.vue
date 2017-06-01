<template>
    <div class="home">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-date"></i> 工单</el-breadcrumb-item>
                <el-breadcrumb-item>工单列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <el-row :gutter="20">
            <el-col :span="6" :offset="18" class="text-right">
                <el-button  type="primary" class="grid-content bg-purple-light border-b-gray" @click="showDialog">提交工单</el-button>
            </el-col>
        </el-row >
        <el-row :gutter="24">
            <el-col :span="22">
                <el-form :inline="true" :model="formSearch" :rules="ruleSearch" ref="formSearch" class="demo-form-inline">
                    <!--<el-form-item>
                        <el-select v-model="formSearch.product" placeholder="请选择产品" prop="product">
                            <el-option label="全部产品" value="0" ></el-option>
                            <el-option label="账号注册" value="1"></el-option>
                            <el-option label="公司注册" value="2"></el-option>
                            <el-option label="商标注册" value="3"></el-option>
                            <el-option label="VAT" value="4"></el-option>
                            <el-option label="分类审核" value="5"></el-option>
                            <el-option label="品牌备案" value="6"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-select v-model="formSearch.priority" placeholder="优先级" prop="priority">
                            <el-option label="紧急" value="1"></el-option>
                            <el-option label="重要" value="2"></el-option>
                            <el-option label="普通" value="3"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-select v-model="formSearch.status" placeholder="状态" prop="status">
                            <el-option label="紧急" value="1"></el-option>
                            <el-option label="重要" value="2"></el-option>
                            <el-option label="普通" value="3"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-date-picker v-model="formSearch.date1" type="date" placeholder="选择日期"
                                        :picker-options="pickerOptions0" prop="date1"></el-date-picker>
                    </el-form-item>
                    <el-form-item>
                        <el-date-picker v-model="formSearch.date2" type="date" placeholder="选择日期"
                                        :picker-options="pickerOptions0" prop="date2"></el-date-picker>
                    </el-form-item>-->
                    <el-form-item  id="search-input">
                        <el-input  v-model="formSearch.search" placeholder="请输入搜索关键字" prop="search"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="onSubmit()">搜索</el-button>
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
                      v-loading="loading2"
                      element-loading-text="拼命加载中"
                      :data="tableData"
                      border
                      style="width: 100%"
                      :row-class-name="tableRowClassName"
                      :default-sort = "{prop: 'serial', order: 'descending'}">
                <el-table-column prop="no" label="编号" width="180" @click="open(id)"></el-table-column>
                <el-table-column prop="title" label="主题" width="170"></el-table-column>
                <el-table-column prop="channel_value" label="销售渠道" width="150"></el-table-column>
                <el-table-column prop="customer_value" label="客户名称" width="250"></el-table-column>
                <el-table-column prop="type_value" label="产品类型" width="150"></el-table-column>
                <el-table-column prop="contract_no" label="合同订单号" width="180"></el-table-column>
                <el-table-column prop="priority_value" sortable label="优先级" width="100"></el-table-column>
                <el-table-column prop="status_value" sortable label="状态" width="100"></el-table-column>
                <el-table-column prop="create_time" sortable label="提交时间" width="120"></el-table-column>
                <el-table-column prop="serial" sortable label="最后更新" width="120"></el-table-column>

                <!-- <el-table-column min-width="300px" label="标题">
                     <template scope="scope">
                         <el-input v-show="editShow" size="small" v-model="scope.row.title"></el-input>
                         <span v-show="!editShow">{{ scope.row.title }}</span>
                     </template>
                 </el-table-column>-->

                <el-table-column label="操作" width="150" >
                    <template scope="scope">
                        <!--<el-button type="small"  icon="edit"-->
                        <!--@click="editData(scope.row.id)">编辑</el-button>-->
                        <!--<el-button size="small" type="danger"  icon="delete"-->
                        <!--@click="deleteData(scope.row.id)">删除</el-button>-->

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



        <!-- 生成工单 -->
        <el-dialog title="提交任务工作单" :visible.sync="workOrderForm">
            <el-form :model="orderForm" :rules="rulesOrder" ref="orderForm" label-width="150px" class="demo-ruleForm">
                <el-form-item label="销售渠道" prop="channel" >
                    <el-select v-model="orderForm.channel" placeholder="请选择销售渠道">
                        <el-option
                            v-for="item in channelList"
                            :key="item.value"
                            :label="item.key_value"
                            :value="item.key_name">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="产品类型" prop="product_type">
                    <el-select v-model="orderForm.product_type" placeholder="请选择产品类型">
                        <el-option
                            v-for="item in productList"
                            :key="item.value"
                            :label="item.key_value"
                            :value="item.key_name">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="客户名称" prop="clientName">
                   <!-- <el-input v-model="orderForm.clientName" placeholder="客户名称" >  </el-input>-->
                    <el-autocomplete v-model="orderForm.clientName"
                                     :fetch-suggestions="querySearchAsync"
                                     placeholder="请输入客户名称"
                                     @select="handleSelect"
                    ></el-autocomplete>
                </el-form-item>
                <el-form-item label="合同订单编号" prop="orderNumber">
                    <el-input v-model="orderForm.orderNumber" placeholder="合同订单编号"></el-input>
                </el-form-item>
                <el-form-item label="优先级" prop="priority">
                    <el-select v-model="orderForm.priority" placeholder="请输入优先级">
                        <el-option
                            v-for="item in priorityList"
                            :key="item.value"
                            :label="item.key_value"
                            :value="item.key_name">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="主题" prop="theme">
                    <el-input v-model="orderForm.theme" placeholder="请输入主题说明"></el-input>
                </el-form-item>
                <el-form-item label="任务说明" prop="taskDescription">
                    <el-input type="textarea" :rows="5" placeholder="请输入内容" v-model="orderForm.taskDescription"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="closeDialog">取 消</el-button>
                <el-button type="primary" @click="upOrder('orderForm')">确 定</el-button>
            </div>
        </el-dialog>
        <!-- 信息详情 -->
      <!--  <el-dialog title="提交任务工作单" :visible.sync="dialogFormVisible">
            <el-form :model="orderFormEdit" :rules="rulesEdit" ref="orderFormEdit" label-width="150px" class="demo-ruleForm">
                <el-form-item label="销售渠道" prop="channel_value">
                    <el-select v-model="orderFormEdit.channel" placeholder="请选择销售渠道">
                        <el-option
                            v-for="item in channelList"
                            :key="item.value"
                            :label="item.key_value"
                            :value="item.key_name" :selected="item.key_name == orderFormEdit.type">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="产品类型" prop="type_value">
                    <el-select v-model="orderFormEdit.type" placeholder="请选择产品类型">
                        <el-option
                            v-for="item in productList"
                            :key="item.value"
                            :label="item.key_value"
                            :value="item.key_name" :selected="item.key_name == orderFormEdit.type">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="客户名称" prop="customer_value">
                    &lt;!&ndash; <el-input v-model="orderForm.clientName" placeholder="客户名称" >  </el-input>&ndash;&gt;
                    <el-autocomplete v-model="orderFormEdit.customer_value"
                                     :fetch-suggestions="querySearchAsync"
                                     placeholder="请输入客户名称"
                                     @select="handleSelectInfo"
                    ></el-autocomplete>
                </el-form-item>
                <el-form-item label="合同订单编号" prop="orderNumber">
                    <el-input v-model="orderFormEdit.contract_no" placeholder="合同订单编号"></el-input>
                </el-form-item>
                <el-form-item label="优先级" prop="priority_value">
                    <el-select v-model="orderFormEdit.priority" placeholder="请输入优先级">
                        <el-option
                            v-for="item in priorityList"
                            :key="item.value"
                            :label="item.key_value"
                            :value="item.key_name" :selected="item.key_name==orderFormEdit.priority">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="主题" prop="title">
                    <el-input v-model="orderFormEdit.title" placeholder="请输入主题说明"></el-input>
                </el-form-item>
                <el-form-item label="任务说明" prop="content">
                    <el-input type="textarea" :rows="5" placeholder="请输入内容" v-model="orderFormEdit.content"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="closeDialogInfo">取 消</el-button>
                <el-button type="primary" @click="deleteDatas('orderFormEdit',orderFormEdit.id)">确 定</el-button>
            </div>
        </el-dialog>-->
    </div>
</template>
<script>
    import qs from 'qs'
    import message from 'element-ui'
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
                pickerOptions0: {         //  时间选择器
                   /* disabledDate(time) {
                        return time.getTime() < Date.now() - 8.64e7;
                    }*/
                },
                workOrderForm:false,     //  生成工作任务单
                orderForm:{
                    channel:'',          //  销售渠道
                    product_type:'',     //  产品类型
                    clientName:'',       //  客户名称
                    clientId:'',         //  客户名称id
                    orderNumber:'',      //  合同订单编号
                    priority:'',         //  优先级
                    theme:'',            //  主题
                    taskDescription:'',  //  任务说明
                },
                rulesOrder:{
                    channel: [
                        {type:'number',required: true, message: '请输入销售渠道', trigger: 'change'}
                    ],
                    product_type:[
                        {type:'number',required: true, message: '请输入产品类型', trigger: 'change'}
                    ],
                    clientName:[
                        {required: true, message: '请输入客户名称', trigger: 'change'}
                    ],
                    orderNumber:[
                        {required: true, message: '请输入合同订单编号', trigger: 'blur'}
                    ],
                    priority:[
                        {type:'number',required: true, message: '请输入优先级', trigger: 'change'}
                    ],
                    theme:[
                        {required: true, message: '请输入主题', trigger: 'blur'}
                    ],
                    taskDescription:[
                        {required: true, message: '请输入任务说明', trigger: 'blur'}
                    ]
                },
                orderFormEdit:{          //  编辑任务单
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
                    status:1             //

                },
                rulesEdit:{
                    channel: [
                        { required: true, message: '请选择活动区域' },
                        { type: 'number', message: '请选择活动区域' },

                    ],
                    type:[
                        {required: true, message: '请输入产品类型', }
                    ],
                    customer_value:[
                        {required: true, message: '请输入客户名称', }
                    ],
                    no:[
                        {required: true, message: '请输入合同订单编号', }
                    ],
                    priority:[
                        {required: true, message: '请输入优先级', }
                    ],
                    title:[
                        {required: true, message: '请输入主题', }
                    ],
                    content:[
                        {required: true, message: '请输入任务说明', }
                    ]
                },

            //  远程搜索
                restaurants: [],         //搜索的列表
                timeout:  null,
            //  远程搜索

                channelList:[],              //  销售类型
                productList:[],              //  产品类型
                priorityList:[],             //  优先级
            }

        },
        created() {
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
                    type: "call"
                })).then( (res) =>{
                    if(res.data.status == 100){
                         self.channelList=res.data.value.detail
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
            //产品类型
            getProduct(){
                let self = this;
                this.$axios.post('/makesCanton/ConstantConfig/getConstant',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    value:"work_type",
                    type: "call"
                })).then( (res) =>{
                    console.log(res);
                    if(res.data.status == 100){
                        self.productList=res.data.value.detail
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
                        self.priorityList=res.data.value.detail;
                        console.log(self.priorityList);
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

            // 获取列表数据
            getList() {
                let self = this;
                this.$axios.post('/makesCanton/Opera/WorkTask/workList',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    search:self.list.search,
                    num:self.list.num,
                    page:self.list.page})).then( (res) =>{
                    self.loading2 = false;
                    console.log(res);
                    if(res.data.status == 100){
                        self.tableData = res.data.value;
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
            // 生成编号
            onSubmit() {
                let self = this;
                this.$axios.post('/makesCanton/Opera/WorkTask/workList',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    search:self.formSearch.search,
                    num:self.list.num,
                    page:self.list.page})).then( (res) =>{
                    self.loading2 = false;
                    if(res.data.status == 100){
                        self.tableData = res.data.value;
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
           /* editData(id) {
                let self = this;
                self.dialogFormVisible = true;
                this.$axios.post('/makesCanton/Opera/WorkTask/workList',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    num:1,
                    page:1,
                    id:id
                    })).then( (res) =>{
                    self.loading2 = false;
                    if(res.data.status == 100){
                        self.orderFormEdit = res.data.value[0];
                        self.formInlineEdit.serial = Number(self.formInlineEdit.serial);
                    }else if(res.data.status == 1012){
                        self.$router.push('/login');
                    }

                })
            },*/
           /* deleteData(id) {
                    var self = this;
                    this.$confirm('是否删除该条信息？', '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(() => {
                        this.$axios.post('/makesCanton/Opera/WorkTask/workClose',qs.stringify({
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
            },*/
          /*  deleteDatas(formInlineEdit,id) {
               /!* Opera/Number/editNumberRule*!/
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
                            }
                            if (res.data.status == 1012) {
                                self.$router.push('/login');
                            }
                        })
                    }
                })
            },*/
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
                self.workOrderForm = false
            },
            closeDialogInfo() {         //关闭表单
                let self = this;
                self.dialogFormVisible = false
            },
            upOrder(formName) {
                let self = this;
                console.log(self.orderForm);
                this.$refs[formName].validate((valid) => {
                    if(valid){
                        this.$axios.post('/makesCanton/Opera/WorkTask/workAdd   ', qs.stringify({
                            key: self.$cookie.get('oKey'),
                            user_id: self.$cookie.get('token'),
                            channel: self.orderForm.channel,
                            type: self.orderForm.product_type,
                            customer_id: self.orderForm.clientId,
                            priority: self.orderForm.priority,
                            contract_no: self.orderForm.orderNumber,
                            title: self.orderForm.theme,
                            content: self.orderForm.taskDescription
                        })).then((res) => {
                            if (res.data.status == 100) {
                                self.getList();
                                self.workOrderForm = false;
                                self.orderForm= {
                                    channel: '',          //  销售渠道
                                    product_type: '',     //  产品类型
                                    clientName: '',       //  客户名称
                                    clientId: '',         //  客户名称id
                                    orderNumber: '',      //  合同订单编号
                                    priority: '',         //  优先级
                                    theme: '',            //  主题
                                    taskDescription: '',  //  任务说明
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


            // 远程模糊搜索
            loadAll(){
                let self = this;
                this.$axios.post('/makesCanton/ApplyVps/getCustom',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    name:self.orderForm.clientName,
                    })).then( (res) =>{
                    if(res.data.status == 100){
                        self.restaurants = res.data.value;
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
                self.orderForm.clientId = item.key
    },
            handleSelectInfo(item) {
                let self = this;
                self.orderFormEdit.customer_id = item.key
            },
            // 远程模糊搜索

            open(id){
                this.$router.push({ path: 'workorderdetails',query: { id: id }})
            }
        },
        watch: {
            '$route' (to, from) {
                console.log(to);
                console.log(from);
            }
        },
        mounted() {
            let self = this;
            self.restaurants = self.loadAll();
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
</style>

