<template>
  <div>
    <Nav v-if="!isMobile()"/>
    <div class="page">
      <Header/>
      <Child/>
    </div>
    <BottomNav v-if="isMobile()" foreground-color='#42A5F5'
  badge-color='#FBC02D'
  background-color='#FFFFFF'
  icon-color='#0000008A' :options="options" v-model="selected"/>
  </div>
</template>

<script>
import '../../node_modules/leaflet/dist/leaflet.js'
import '../../node_modules/vue2-leaflet'
import Nav from '@/components/Nav'
import BottomNav from '@/components/BottomNav'
import Header from '@/components/Header'
import Child from '@/components/Child'


export default {
	name: 'Default',

	components: {
    Nav,
    Header,
    Child,
    BottomNav
},
data: () => ({
      selected: 1,
      options: [
        // {
        //   id: 1,
        //   icon: "chart-line",
        //   title: "Dashboard",
        //   customImage: false,
        //   isActive: false,
        //   path: { name: "dashboard.index" }
        // },
        {
          id: 1,
          icon: "nav_hstd",
          title: "Hồ sơ thẩm định",
          customImage: true,
          isActive: false,
          path: { name: "certification_brief.index" }
        },
        { id: 2, icon: "nav_tstd",
         title: "Tài sản thẩm định",
          customImage: true ,
          isActive: false,
          childs: [
            { id: 201, icon: "icon_bds", title: "BĐS" , customImage: true, path: { name: "certification_asset.index" }},
            { id: 202, icon: "icon_ds", title: "ĐS", customImage: true, path: { name: "certification_personal_property.index" }},
          ]
        },
        {
          id: 3,
          icon: "nav_bdg",
          title: "Bản đồ giá",
          customImage: true,
          isActive: false,
          path: { name: "home" }
        },
        { id: 4, icon: "nav_kg", title: "Kho giá", isActive: false,customImage: true,path: { name: "warehouse.index" }},
        { id: 5, icon: "users-cog", title: "Tài khoản", isActive: false,customImage: false,path: { name: "profile.index" }},
      ],
    }),
  methods: {
    isMobile() {
      if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        return true
      } else {
        return false
      }
    },
	}
}
</script>
