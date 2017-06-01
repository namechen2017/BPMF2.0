<template>
    <div class="creat">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-menu"></i> 工单</el-breadcrumb-item>
                <el-breadcrumb-item>生成工单</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="form-box">
            <el-autocomplete
                v-model="state4"
                :fetch-suggestions="querySearchAsyncA"
                placeholder="请输入内容"
                @select="handleSelectA"
            ></el-autocomplete>
            <el-autocomplete
                v-model="state"
                :fetch-suggestions="querySearchAsyncB"
                placeholder="请输入内容"
                @select="handleSelectB"
            ></el-autocomplete>
        </div>
        <div>
            Counter is: {{ count }}
            <button @click="increment">+</button>
            <button @click="idecrement">-</button>
        </div>
    </div>
</template>
<script>
    import { mapGetter,mapActions } from 'vuex'
    import qs from 'qs'
    export default {
        data: function(){
            return {
                restaurants: [],
                restaurant: [],
                state4: '',
                state: '',
                timeout:  null
            };
        },
        created(){

        },
        computed:mapGetter([]),
        methods: mapActions([
            'increment',
            'decrement'
        ]),
       /* methods: {
            loadAll(){
                let self = this;
                /!*ApplyVps/getCustom*!/
                this.$axios.post('/makesCanton/get/user',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    search:self.state4,
                })).then( (res) =>{
                    if(res.data.status == 100){
                        self.restaurants = res.data.value;
                        self.restaurants.forEach(function(item){
                            // element ui远程搜索显示的列表的的键值固定为value了，因此需要将我们需要的数据赋值给value
                            //先获得要修改键的值，避免丢失。
                            let temp=item.real_name;
                           /!* delete(item.real_name);*!/
                            //再定义新的键和值
                            item.value=temp;
                        })
                    }else if(res.data.status == 1012){
                        self.$router.push('/login');
                    }
                });
            },
            loadBall() {
                let self = this;
                this.$axios.post('/makesCanton/ApplyVps/getCustom',qs.stringify({
                    key:self.$cookie.get('oKey'),
                    user_id:self.$cookie.get('token'),
                    search:self.state,
                })).then( (res) =>{
                    if(res.data.status == 100){
                        self.restaurant = res.data.value;
                    }else if(res.data.status == 1012){
                        self.$router.push('/login');
                    }
                })
            },
            querySearchAsyncA(queryString, cb) {
                let restaurants = this.restaurants;
                let results = queryString ? restaurants.filter(this.createStateFilterA(queryString)) : restaurants;
                console.log(results);
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    cb(results);
                    console.log( cb(results))
                }, 3000 * Math.random());
            },
            querySearchAsyncB(queryString, cb) {
                let restaurant = this.restaurant;
                let results = queryString ? restaurant.filter(this.createStateFilterB(queryString)) : restaurant;
                console.log(results);
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    cb(results);
                    console.log( cb(results))
                }, 3000 * Math.random());
            },
            createStateFilterA(queryString) {
                return (state) => {
                    return (state.value.indexOf(queryString.toLowerCase()) === 0);
                };
            },
            createStateFilterB(queryString) {
                return (state) => {
                    console.log(state);
                    return (state.value.indexOf(queryString.toLowerCase()) === 0);
                };
            },
            handleSelectA(item) {
                console.log(item);
            },
            handleSelectB(item) {
                console.log(item);
            },
        },*/
        mounted() {
            let self = this;
            self.restaurants = self.loadAll();
            console.log(self.restaurants);
            self.restaurant = this.loadBall();
            console.log(self.restaurant);
        }
    }
</script>
<style>


</style>

