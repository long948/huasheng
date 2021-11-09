import Vue from 'vue'
import Router from 'vue-router'
/* Layout */
import Layout from '@/layout'

Vue.use(Router)

export const constantRoutes = [
  {
    path: '/redirect',
    component: Layout,
    hidden: true,
    children: [{
      path: '/redirect/:path*',
      component: () => import('@/views/redirect/index')
    }]
  },
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true
  },
  {
    path: '/auth-redirect',
    component: () => import('@/views/login/auth-redirect'),
    hidden: true
  },
  {
    path: '/404',
    component: () => import('@/views/error-page/404'),
    hidden: true
  },
  {
    path: '/401',
    component: () => import('@/views/error-page/401'),
    hidden: true
  },
  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    children: [{
      path: 'dashboard',
      component: () => import('@/views/dashboard/index'),
      name: 'Dashboard',
      meta: {
        title: '首页',
        icon: 'example',
        affix: true
      }
    }]
  }
]

/**
 * asyncRoutes
 * the routes that need to be dynamically loaded based on user roles
 */
export const asyncRoutes = [
  // {
  //   path: '/merchant',
  //   component: Layout,
  //   redirect: '/merchant/list',
  //   meta: {
  //     title: '商户',
  //     icon: 'table'
  //   },
  //   children: [
  //     {
  //       path: 'list',
  //       component: () => import('@/views/merchant/list'),
  //       name: 'MerchantList',
  //       meta: {
  //         title: '商户列表',
  //         icon: 'list',
  //         noCache: true
  //       }
  //     }
  //   ]
  // },
  {
    path: '/config',
    component: Layout,
    redirect: '/config/list',
    meta: {
      title: '配置管理',
      icon: 'table'
    },
    children: [{
      path: 'list',
      component: () => import('@/views/config/list'),
      name: 'List',
      meta: {
        title: '七牛配置',
        icon: 'table',
        noCache: true
      }
    },
    {
      path: 'appList',
      component: () => import('@/views/config/appList'),
      name: 'appList',
      meta: {
        title: '版本配置',
        icon: 'table',
        noCache: true
      }
    },
    {
      path: 'share',
      component: () => import('@/views/config/share'),
      name: 'share',
      meta: {
        title: '分享配置',
        icon: 'example',
        noCache: true
      }
    },
    {
      path: 'GiveSettingList',
      component: () => import('@/views/config/give_setting'),
      name: 'GiveSettingList',
      meta: {
        title: '分享赠送配置',
        icon: 'example',
        noCache: true
      }
    },
    {
      path: 'downloadLink',
      component: () => import('@/views/config/appDownloadLink'),
      name: 'downloadLink',
      meta: {
        title: 'App下载链接',
        icon: 'example',
        noCache: true
      }
    }
    // {
    //   path: 'smsList',
    //   component: () => import('@/views/config/smsList'),
    //   name: 'smsList',
    //   meta: {
    //     title: '短信配置',
    //     icon: 'table',
    //     noCache: true
    //   }
    // },
    // {
    //   path: 'smstemplateList',
    //   component: () => import('@/views/config/smsTemplate'),
    //   name: 'smstemplateList',
    //   meta: {
    //     title: '短信模板',
    //     icon: 'table',
    //     noCache: true
    //   }
    // }
    ]
  },
  // {
  //   path: '/setting',
  //   component: Layout,
  //   redirect: '/setting/invite',
  //   meta: {
  //     title: '通用设置',
  //     icon: 'table'
  //   },
  //   children: [
  //     // {
  //     //   path: 'invite',
  //     //   component: () => import('@/views/setting/invite'),
  //     //   name: 'SettingInvite',
  //     //   meta: {
  //     //     title: '邀请奖励设置',
  //     //     icon: 'tree-table',
  //     //     noCache: true
  //     //   }
  //     // },
  //     {
  //       path: 'other',
  //       component: () => import('@/views/setting/other'),
  //       name: 'SettingOther',
  //       meta: {
  //         title: '其他设置',
  //         icon: 'tree-table',
  //         noCache: true
  //       }
  //     }
  //   ]
  // },
  {
    path: '/rule',
    component: Layout,
    redirect: '/rule/list',
    meta: {
      title: '权限管理',
      icon: 'documentation'
    },
    children: [{
      path: 'list',
      component: () => import('@/views/role/list'),
      name: 'RoleList',
      meta: {
        title: '权限列表',
        icon: 'documentation',
        noCache: true
      }
    },
    {
      path: 'group',
      component: () => import('@/views/role/group'),
      name: 'RoleGroup',
      meta: {
        title: '权限组',
        icon: 'documentation',
        noCache: true
      }
    },
    {
      path: 'group/edit/:id',
      hidden: true,
      component: () => import('@/views/role/edit-group'),
      name: 'GroupEdit',
      meta: {
        title: '编辑权限组',
        icon: 'documentation',
        noCache: true
      }
    },
    {
      path: 'group/add',
      hidden: true,
      component: () => import('@/views/role/add-group'),
      name: 'GroupAdd',
      meta: {
        title: '添加权限组',
        icon: 'documentation',
        noCache: true
      }
    }
    ]
  },
  // {
  //   path: '/trade',
  //   component: Layout,
  //   redirect: '/trade/list',
  //   meta: {
  //     title: '订单',
  //     icon: 'table'
  //   },
  //   children: [
  //     {
  //       path: 'list',
  //       component: () => import('@/views/trade/list'),
  //       name: 'TradeList',
  //       meta: {
  //         title: '订单列表',
  //         icon: 'tree-table',
  //         noCache: true
  //       }
  //     }
  //   ]
  // },
  {
    path: '/admin',
    component: Layout,
    redirect: '/admin/list',
    meta: {
      title: '管理员管理',
      icon: 'user'
    },
    children: [{
      path: 'list',
      component: () => import('@/views/admin/list'),
      name: 'AdminList',
      meta: {
        title: '管理员列表',
        icon: 'user',
        noCache: true
      }
    },
    {
      path: 'adminLogList',
      component: () => import('@/views/admin/adminLogList'),
      name: 'adminLogList',
      meta: {
        title: '操作日志',
        icon: 'user',
        noCache: true
      }
    }
    ]
  },
  {
    path: '/members',
    component: Layout,
    redirect: '/members/list',
    meta: {
      title: '会员管理',
      icon: 'peoples'
    },
    children: [
      {
        path: 'list',
        component: () => import('@/views/members/list'),
        name: 'list',
        meta: {
          title: '会员列表',
          icon: 'peoples'
        }
      },
      {
        path: 'auth',
        component: () => import('@/views/members/auth'),
        name: 'auth',
        meta: {
          title: '实名审核',
          icon: 'peoples'
        }
      },
      {
        path: 'user_sapling_release',
        component: () => import('@/views/miner/sapling_release'),
        name: 'user_sapling_release',
        meta: {
          title: '会员花田释放记录',
          icon: 'list',
          noCache: true
        }
      },
      {
        path: 'ecology_order_list',
        component: () => import('@/views/members/ecology_order'),
        name: 'ecology_order_list',
        meta: {
          title: '油卡/话费充值订单',
          icon: 'peoples',
          noCache: true
        }
      },
      {
        path: 'whitelist',
        component: () => import('@/views/members/white_list'),
        name: 'whitelist',
        meta: {
          title: '交易白名单',
          icon: 'peoples',
          noCache: true
        }
      },
      {
        path: 'user_amount',
        component: () => import('@/views/members/UserAmount'),
        name: 'user_amount',
        meta: {
          title: '会员资产排序',
          icon: 'peoples',
          noCache: true
        }
      },
      {
        path: 'computing_power',
        component: () => import('@/views/members/UserComputingPower'),
        name: 'computing_power',
        meta: {
          title: '会员算力记录',
          icon: 'peoples',
          noCache: true
        }
      },
      {
        path: 'equityDividend',
        component: () => import('@/views/members/test'),
        name: 'test',
        meta: {
          title: '超市权益分红',
          icon: 'peoples',
          noCache: true
        }
      },
      {
        path: 'user_levelList',
        component: () => import('@/views/miner/user_level'),
        name: 'user_levelList',
        meta: {
          title: '会员平台等级',
          icon: 'list',
          noCache: true
        }
      },
      {
        path: 'user_level',
        component: () => import('@/views/members/setting_user_level'),
        name: 'user_level',
        meta: {
          title: '交易等级规则',
          icon: 'list',
          noCache: true
        }
      },
      {
        path: 'RegularGrade',
        component: () => import('@/views/members/regularGrade'),
        name: 'RegularGrade',
        meta: {
          title: '常规等级规则',
          icon: 'list',
          noCache: true
        }
      },
      {
        path: 'user_dividendList',
        component: () => import('@/views/members/user_dividend_record'),
        name: 'user_dividendList',
        meta: {
          title: '会员分红记录',
          icon: 'peoples',
          noCache: true
        }
      },
      {
        path: 'signList',
        component: () => import('@/views/members/signList'),
        name: 'signList',
        meta: {
          title: '签到记录',
          icon: 'peoples',
          noCache: true
        }
      },
      // {
      //   path: 'user_sapling_package',
      //   component: () => import('@/views/members/user_sapling_package'),
      //   name: 'user_sapling_package',
      //   meta: {
      //     title: '会员开通的机器人',
      //     icon: 'peoples',
      //     noCache: true
      //   }
      // },
      {
        path: 'share_reward_record',
        component: () => import('@/views/members/share_reward_record'),
        name: 'share_reward_record',
        meta: {
          title: '会员分享奖励记录',
          icon: 'peoples',
          noCache: true
        }
      },

      {
        path: 'Admin_operation_record',
        component: () => import('@/views/members/admin_operation_record'),
        name: 'Admin_operation_record',
        meta: {
          title: '管理员充值记录',
          icon: 'peoples',
          noCache: true
        }
      },
      {
        path: 'give_sapling',
        component: () => import('@/views/members/admin_give_sapling'),
        name: 'give_sapling',
        meta: {
          title: '管理员赠送树苗记录',
          icon: 'peoples',
          noCache: true
        }
      }
    ]
  },
  {
    path: '/ctc',
    component: Layout,
    redirect: '/ctc/order',
    meta: {
      title: 'CTC管理',
      icon: 'table'
    },
    children: [
      {
        path: 'order',
        component: () => import('@/views/ctc/order'),
        name: 'CTCOrder',
        meta: {
          title: 'CTC订单',
          icon: 'tree-table',
          noCache: true
        }
      },
      {
        path: 'trade',
        component: () => import('@/views/ctc/trade'),
        name: 'CTCTrade',
        meta: {
          title: 'CTC交易',
          icon: 'tree-table',
          noCache: true
        }
      },
      // {
      //     path: 'TransactionFee',
      //     component: () => import('@/views/ctc/transactionfee'),
      //     name: 'TransactionFee',
      //     meta: {
      //         title: '交易手续费',
      //         icon: 'tree-table',
      //         noCache: true
      //     }
      // },
      {
        path: 'trade_rule',
        component: () => import('@/views/ctc/traderule'),
        name: 'trade_rule',
        meta: {
          title: '交易规则',
          icon: 'tree-table',
          noCache: true
        }
      },
      {
        path: 'trade_guidance',
        component: () => import('@/views/ctc/tradeguidance'),
        name: 'trade_guidance',
        meta: {
          title: '交易指导',
          icon: 'tree-table',
          noCache: true
        }
      },
      {
        path: 'forest_rule',
        component: () => import('@/views/ctc/forestrule'),
        name: 'forest_rule',
        meta: {
          title: '花生世界规则',
          icon: 'tree-table',
          noCache: true
        }
      },
      {
        path: 'appeal',
        component: () => import('@/views/ctc/appeal'),
        name: 'CTCAppeal',
        meta: {
          title: '申诉管理',
          icon: 'tree-table',
          noCache: true
        }
      },
      {
        path: 'setting',
        component: () => import('@/views/setting/ctc'),
        name: 'SettingCTC',
        meta: {
          title: 'CTC设置',
          icon: 'tree-table',
          noCache: true
        }
      },
      {
        path: 'ctccoin',
        component: () => import('@/views/setting/ctccoin'),
        name: 'CTCCoin',
        meta: {
          title: 'CTC币种设置',
          icon: 'tree-table',
          noCache: true
        }
      },
      {
        path: 'other',
        component: () => import('@/views/setting/other'),
        name: 'SettingOther',
        meta: {
          title: '其他设置',
          icon: 'tree-table',
          noCache: true
        }
      }
    ]
  },
  {
    path: '/miner',
    component: Layout,
    redirect: '/miner/miner_level',
    meta: {
      title: '矿机管理',
      icon: 'table'
    },
    children: [{
      path: 'miner_level',
      component: () => import('@/views/miner/miner_level'),
      name: 'miner_level',
      meta: {
        title: '平台等级',
        icon: 'list',
        noCache: true
      }
    },
    {
      path: 'miner_saplingType',
      component: () => import('@/views/miner/saplingtype'),
      name: 'miner_saplingType',
      meta: {
        title: '树苗类型',
        icon: 'list',
        noCache: true
      }
    },

    {
      path: 'miner_dividend',
      component: () => import('@/views/miner/miner_dividend'),
      name: 'miner_dividend',
      meta: {
        title: '分红配置',
        icon: 'list',
        noCache: true
      }
    },
    {
      path: 'sapling_share_reward',
      component: () => import('@/views/miner/share_reward'),
      name: 'sapling_share_reward',
      meta: {
        title: '分享奖励规则',
        icon: 'list',
        noCache: true
      }
    },
    {
      path: 'user_sapling_receive',
      component: () => import('@/views/miner/user_sapling_receive'),
      name: 'user_sapling_receive',
      meta: {
        title: '用户可领取树苗收益',
        icon: 'list',
        noCache: true
      }
    }
    ]
  },
  {
    path: '/coin',
    component: Layout,
    redirect: '/coin/coinList',
    meta: {
      title: '币种管理',
      icon: 'money'
    },
    children: [{
      path: 'coinList',
      component: () => import('@/views/coin/coinList'),
      name: 'coinList',
      meta: {
        title: '币种列表',
        icon: 'money',
        noCache: true
      }
    },
    {
      path: 'rechargeList',
      component: () => import('@/views/coin/rechargeList'),
      name: 'rechargeList',
      meta: { title: '转入记录', icon: 'money', noCache: true }
    },
    {
      path: 'withdrawList',
      component: () => import('@/views/coin/withdrawList'),
      name: 'withdrawList',
      meta: { title: '转出记录', icon: 'money', noCache: true }
    }
    // {
    //   path: 'TransferRecord',
    //   component: () => import('@/views/coin/exchange'),
    //   name: 'TransferRecord',
    //   meta: { title: '转账记录', icon: 'money', noCache: true }
    // }
    ]
  },
  {
    path: '/shop',
    component: Layout,
    redirect: '/shop/flower',
    meta: {
      title: '商店管理',
      icon: 'table'
    },
    children: [{
      path: 'flower',
      component: () => import('@/views/shop/flowerField'),
      name: 'flower',
      meta: {
        title: '花田列表',
        icon: 'table',
        noCache: true
      }
    },
    {
      path: 'user_sapling',
      component: () => import('@/views/miner/user_sapling'),
      name: 'user_sapling',
      meta: {
        title: '会员花田列表',
        icon: 'list',
        noCache: true
      }
    },
    {
      path: 'sapling_package',
      component: () => import('@/views/shop/mouseList'),
      name: 'sapling_package',
      meta: {
        title: '老鼠列表',
        icon: 'list',
        noCache: true
      }
    },
    {
      path: 'user_sapling_package',
      component: () => import('@/views/shop/userMouseList'),
      name: 'user_sapling_package',
      meta: {
        title: '会员的老鼠',
        icon: 'peoples',
        noCache: true
      }
    },
    {
      path: 'dog_list',
      component: () => import('@/views/shop/dogList'),
      name: 'dog_list',
      meta: {
        title: '小狗列表',
        icon: 'list',
        noCache: true
      }
    },
    {
      path: 'user_dog_list',
      component: () => import('@/views/shop/userDogList'),
      name: 'user_dog_list',
      meta: {
        title: '会员小狗列表',
        icon: 'list',
        noCache: true
      }
    }
    ]
  },
  {
    path: '/market',
    component: Layout,
    redirect: '/market/list',
    meta: {
      title: '营销管理',
      icon: 'sms'
    },
    children: [{
      path: 'goods_list',
      component: () => import('@/views/market/goods'),
      name: 'GoodsList',
      meta: {
        title: '商品列表',
        icon: 'sms',
        noCache: false
      }
    },
    {
      path: 'pg_goods',
      component: () => import('@/views/market/Pg'),
      name: 'PgGoods',
      meta: {
        title: '拼购商品列表',
        icon: 'sms',
        noCache: true
      }
    },
    {
      path: 'activity',
      component: () => import('@/views/market/activity'),
      name: 'activity',
      meta: {
        title: '拼购活动列表',
        icon: 'sms',
        noCache: true
      }
    },
    {
      path: 'team_found',
      component: () => import('@/views/market/teamFound'),
      name: 'team_found',
      meta: {
        title: '开团记录',
        icon: 'sms',
        noCache: true

      }
    },
    {
      path: 'team_follow',
      component: () => import('@/views/market/teamFollow'),
      name: 'team_follow',
      meta: {
        title: '参团记录',
        icon: 'sms',
        noCache: true
      }
    },
    {
      path: 'team_lottery',
      component: () => import('@/views/market/teamLottery'),
      name: 'team_lottery',
      meta: {
        title: '中奖记录',
        icon: 'sms',
        noCache: true
      }
    },
    {
      path: 'order_setting',
      component: () => import('@/views/market/setting'),
      name: 'OrderSetting',
      meta: {
        title: '通用设置',
        icon: 'sms',
        noCache: true
      }
    }
    ]
  },
  {
    path: '/bannerNotice',
    component: Layout,
    redirect: '/bannerNotice/bannerList',
    meta: {
      title: '广告管理',
      icon: 'example'
    },
    children: [
      {
        path: 'bannerList',
        component: () => import('@/views/banner/list'),
        name: 'bannerList',
        meta: {
          title: 'banner列表',
          icon: 'example',
          noCache: true
        }
      },
      {
        path: 'noticeList',
        component: () => import('@/views/notice/list'),
        name: 'noticeList',
        meta: {
          title: '通知消息',
          icon: 'example',
          noCache: true
        }
      },
      {
        path: 'notice',
        component: () => import('@/views/notice/Notice'),
        name: 'notice',
        meta: {
          title: '首页公告',
          icon: 'example',
          noCache: true
        }
      },
      {
        path: 'MemberDoc',
        component: () => import('@/views/notice/MemberDoc'),
        name: 'MemberDoc',
        meta: {
          title: '用户协议',
          icon: 'example',
          noCache: true
        }
      },
      {
        path: 'user_feedback',
        component: () => import('@/views/notice/userFeedback'),
        name: 'user_feedback',
        meta: {
          title: '用户反馈',
          icon: 'example',
          noCache: true
        }
      },
      // {
      //   path: 'PayDoc',
      //   component: () => import('@/views/notice/PayDoc'),
      //   name: 'PayDoc',
      //   meta: {
      //     title: '支付协议',
      //     icon: 'example',
      //     noCache: true
      //   }
      // },
      {
        path: 'schoolList',
        component: () => import('@/views/notice/school'),
        name: 'schoolList',
        meta: {
          title: '商学院文章',
          icon: 'example',
          noCache: true
        }
      },
      {
        path: 'qa',
        component: () => import('@/views/notice/qa'),
        name: 'QA',
        meta: {
          title: '常见问题',
          icon: 'example',
          noCache: true
        }
      },
      {
        path: 'server',
        component: () => import('@/views/notice/server'),
        name: 'QA',
        meta: {
          title: '客服',
          icon: 'example',
          noCache: true
        }
      },
      {
        path: 'pg_rule',
        component: () => import('@/views/notice/pgRule'),
        name: 'pgRule',
        meta: {
          title: '拼购规则',
          icon: 'example',
          noCache: true
        }
      }
      // {
      //   path: 'wechatGroup',
      //   component: () => import('@/views/banner/wechat'),
      //   name: 'WechatGroup',
      //   meta: {
      //     title: '微信群',
      //     icon: 'example',
      //     noCache: true
      //   }
      // }
    ]
  },
  {
    path: '/system',
    component: Layout,
    redirect: '/system/systemList',
    meta: {
      title: '系统维护',
      icon: 'table'
    },
    children: [
      {
        path: 'systemList',
        component: () => import('@/views/config/system'),
        name: 'systemList',
        meta: {
          title: '系统维护',
          icon: 'tree-table',
          noCache: true
        }
      }
    ]
  },
  {
    path: '*',
    redirect: '/404',
    hidden: true
  }
]

const createRouter = () => new Router({
  // mode: 'history', // require service support
  scrollBehavior: () => ({
    y: 0
  }),
  routes: constantRoutes
})

const router = createRouter()

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher // reset router
}

export default router
