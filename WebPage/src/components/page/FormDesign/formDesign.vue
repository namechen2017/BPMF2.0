<template>
    <div >
        <div class="title"><i class="el-icon-arrow-left" @click="comeback"></i>添加新字段-选择字段类型</div>
        <el-table
            :data="channelList"
            border
            style="width: 100%">
            <el-table-column  align="center" label="类型名称" width="300">
                <template scope="scope" >
                    <span>{{scope.row.key_value}}</span>
                </template>
            </el-table-column>

            <el-table-column  label="类型模板"  >
                <template scope="scope">
                    <el-input :type="scope.row.type" placeholder="单行文本"  v-show="scope.row.key_name == 1 "></el-input>

                    <el-select  v-model="dataList.value" placeholder="" v-show="scope.row.key_name == 2 ">
                        <el-option :label="item.label" :value="item.value" v-for="item in options"></el-option>
                    </el-select>

                    <el-time-select v-show="scope.row.key_name == 3 " v-model="dataList.time"
                                    :picker-options="{start: '08:30',step: '00:15',end: '18:30'}"
                                    placeholder="选择时间">
                    </el-time-select>

                    <el-date-picker
                        v-model="dataList.date" v-show="scope.row.key_name == 4 "
                        type="date"
                        placeholder="选择日期时间">
                    </el-date-picker>

                    <el-date-picker v-show="scope.row.key_name == 5 " v-model="dataList.dateTime"
                                    type="datetime"
                                    placeholder="选择日期时间">
                    </el-date-picker>

                    <el-input type="number" placeholder="数字"  v-show="scope.row.key_name == 6 " v-model="dataList.number"></el-input>

                    <el-input type="number" placeholder="小数"  v-show="scope.row.key_name == 7 " v-model="dataList.decimal"></el-input>

                    <el-radio-group v-model="radio" v-show="scope.row.key_name == 8 ">
                        <el-radio :label="item.label" :value="item.value" v-for="item in radioOptions"></el-radio>
                    </el-radio-group>

                    <el-checkbox-group v-model="list"  v-show="scope.row.key_name == 9 ">
                        <el-checkbox :label="item.label" v-for="item in checkboxOptions"></el-checkbox>
                    </el-checkbox-group>

                    <el-input type="textarea" placeholder="多行文本"  v-show="scope.row.key_name == 10 " v-model="dataList.textarea"></el-input>


                </template>
            </el-table-column>
            <el-table-column  label="类型描述" >
                <template scope="scope">
                    <span>{{scope.row.remark}}</span>
                </template>
            </el-table-column>
            <el-table-column align="center" label="操作" width="200">
                <template scope="scope">
                    <el-button size="small" type="info"
                               @click="open(scope.row.key_name)">增加</el-button>
                </template>
            </el-table-column>
        </el-table>
    </div>

</template>

<script>
    import qs from 'qs'
    export default {
        data() {
            return {
                dataList:{
                    value:'',
                    time:'',
                    date:'',
                    dateTime:'',
                    decimal:'',
                    number:'',
                    textarea:'',
                },
                list:[],
                radio:'',
                options:[
                    {label:'第一项',value:'1'},
                    {label:'第二项',value:'2'}

                ],
                checkboxOptions:[
                    {label:'是',value:'0'},
                    {label:'否',value:'1'},
                ],
                radioOptions:[
                    {label:'选项1',value:'1'},
                    {label:'选项2',value:'2'},
                ],
               /* tableData: [
                    {
                        name: '单行文本',
                        type: 'text',
                        info: '获取少量文本',
                        key_name:''
                    },{
                        name: '下拉列表',
                        type: 'select',
                        options:[
                            {label:'第一项',value:'1'},
                            {label:'第二项',value:'2'}
                        ],
                        info: '下拉列表，多个选项中选择一个'
                    },{
                        name: '多行文本',
                        type: 'textarea',
                        info: '获取大量文本'
                    },{
                        name: '整数',
                        type: 'number',
                        info: '只包括整数，如123'
                    },{
                        name: '小数',
                        type: 'decimal',
                        info: '还包括小数，如123,123.1'
                    },{
                        name: '日期',
                        type: 'time',
                        info: '输入日期，比如1991-09-12'
                    },{
                        name: '时间',
                        type: 'date',
                        info: '输入时间，比如  12:34'
                    },{
                        name: '日期时间',
                        type: 'datetime',
                        info: '输入时间，比如 1991-09-12 12:34'
                    },{
                        name: '单选框',
                        type: 'radio',
                        options: [
                            {label:'是',value:'0'},
                            {label:'否',value:'1'},
                        ],
                        info: '单选框，多个选择中选择1个'
                    },{
                        name: '多选框',
                        type: 'checkbox',
                        options: [
                            {label:'选项1',value:'1'},
                            {label:'选项2',value:'2'},
                        ],
                        info: '多选框，多个选择中选择1个'
                    }
                ],*/
                channelList:[]

            }
        },created(){
                this.getChannel();
        },
        methods:{
            getChannel(){
                let self = this;
                this.$axios.post('/makesCanton/ConstantConfig/getConstant',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    value:"form_component_type",
                    type: 'call'
                })).then( (res) =>{
                    if(res.data.status == 100){
                        self.channelList=res.data.value.detail;
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
            open(type){
                let self = this;
              /* if(type == 'select' || type == "radio"  || type == "checkbox" ){
                   self.$router.push({ path: '/editform',query: { type: type,id:self.$route.query.id}})
               }else {
                   self.$router.push({ path: 'forminfo',query: { type: type,id:self.$route.query.id}})
               }*/
                self.$router.push({ path: 'forminfo',query: { type: type,form_id:self.$route.query.form_id}})
            },
            comeback() {
                let self = this;
                self.$router.push({ path: 'designform',query:{form_id: self.$route.query.form_id}});
            }

        }
    }
</script>

<style scoped>
    .title{
        padding: 15px 10px;
        color: #00B38B
    }
</style>
