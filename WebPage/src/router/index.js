import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

export default new Router({
    routes: [
        {
            path: '/',
            redirect: '/login'
        },
        {
            path: '/readme',
            component: resolve => require(['../components/common/Home.vue'], resolve),
            children:[
                {
                    path: '/',
                    component: resolve => require(['../components/page/Readme.vue'], resolve)
                },
                {
                    path: '/basetable',
                    component: resolve => require(['../components/page/BaseTable.vue'], resolve)
                },
                {
                    path: '/vuetable',
                    component: resolve => require(['../components/page/VueTable.vue'], resolve)     // vue-datasource组件
                },
                {
                    path: '/baseform',
                    component: resolve => require(['../components/page/BaseForm.vue'], resolve)
                },
                {
                    path: '/vueeditor',
                    component: resolve => require(['../components/page/VueEditor.vue'], resolve)    // Vue-Quill-Editor组件
                },
                {
                    path: '/markdown',
                    component: resolve => require(['../components/page/Markdown.vue'], resolve)     // Vue-Quill-Editor组件
                },
                {
                    path: '/upload',
                    component: resolve => require(['../components/page/Upload.vue'], resolve)       // Vue-Core-Image-Upload组件
                },
                {
                    path: '/basecharts',
                    component: resolve => require(['../components/page/BaseCharts.vue'], resolve)   // vue-echarts-v3组件
                },
                {
                    path: '/mixcharts',
                    component: resolve => require(['../components/page/MixCharts.vue'], resolve)    // vue-echarts-v3组件
                },
                {
                    path: '/rules',
                    component: resolve => require(['../components/page/rules.vue'], resolve)    // 定义编号规则
                },
                {
                    path: '/ruleslist',
                    component: resolve => require(['../components/page/rulesList.vue'], resolve)    // 编号列表
                },
                {
                    path: '/workorderlists',
                    component: resolve => require(['../components/page/workOrder/home.vue'], resolve)    // 工单列表
                },
                {
                    path: '/workorderdetails',
                    component: resolve => require(['../components/page/workOrder/details.vue'], resolve)    // 工单详情列表
                },
                {
                    path: '/workordercreat',
                        component: resolve => require(['../components/page/workOrder/creatOrder.vue'], resolve)    // 工单详情列表
                },
                {
                    path: '/testDesk',
                    component: resolve => require(['../components/page/workOrder/testDesk.vue'], resolve)    // 工单任务台
                },
                {
                    path: '/designform',
                    component: resolve => require(['../components/page/FormDesign/designForm.vue'], resolve)    // 表单设计
                },
                {
                    path: '/formdesign',
                    component: resolve => require(['../components/page/FormDesign/formDesign.vue'], resolve)    // 表单编辑
                },
                {
                    path: '/forminfo',
                    component: resolve => require(['../components/page/FormDesign/formInfo.vue'], resolve)    // 表单列表
                },
                {
                    path: '/editform',
                    component: resolve => require(['../components/page/FormDesign/editForm.vue'], resolve)    // 表单编辑
                },
                {
                    path: '/homeform',
                    component: resolve => require(['../components/page/FormDesign/home.vue'], resolve)    // 表单编辑
                }
            ]
        },
        {
            path: '/login',
            component: resolve => require(['../components/page/Login.vue'], resolve)
        },
    ]
})
