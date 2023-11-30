<template>
	<div class="" style="margin: 0;" :style="{ 'width': resizeWidth }">
		<iframe :src="url_modal" frameborder="0" width="100%" height="820dvh"></iframe>
	</div>
</template>
<style lang="scss">
@import "../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import "../../../node_modules/leaflet/dist/leaflet.css";
</style>
<script>
import {
	LMap,
	LControlZoom,
	LTileLayer,
	LMarker,
	LTooltip,
	LIcon,
	LControl,
	LControlLayers,
	LGeoJson,
	LPopup,
	LFeatureGroup,
	LPolygon
} from "vue2-leaflet";
import Vue from "vue";
import Icon from "buefy";
import InputText from "@/components/Form/InputText";
import axios from '@/plugins/axios'
import File from '@/models/File'
import InputSwitchToThua from '@/components/Form/InputSwitchToThua'
import cityJson from '@/assets/json/phuluc_dmhc/city/city.json'
import InputCategory from '@/components/Form/InputCategory'


Vue.use(Icon);
export default {
	name: "ModalMapAsset",
	props: ["location", "address", "center_map"],
	components: {
		LMap,
		LControlZoom,
		LTileLayer,
		LMarker,
		LTooltip,
		LControl,
		LIcon,
		LPopup,
		InputText,
		LControlLayers,
		LGeoJson,
		InputSwitchToThua,
		InputCategory,
		LFeatureGroup,
		LPolygon
	},
	data() {
		return {
			url_modal: 'https://dev-web.fastvalue.com.vn/xem-quy-hoach/?mode=noHeader',
			seg_key: 0,
			progress: 0,
			emptyColorFill: {
			radial: false,
			colors: [
				{
				color: "#754fc1",
				offset: "0",
				opacity: "0.3",
				},
				{
				color: "#366bfc",
				offset: "100",
				opacity: "0.3",
				},
			],
			},
			search_to_thua: false,
			dataResult: [],
			isOpen: false,
			url_quyhoach: "",
			tileProviders: [
				{
					name: "Bản đồ ranh tờ, thửa",
					visible: true,
					url: "https://cdn.estatemanner.com/tile/ranh_thua/{z}/{x}/{y}.png",
					attribution: "© Fastvalue",
					type: "overlay"
				},
				{
					name: "Bản đồ thông tin quy hoạch",
					visible: false,
					attribution: "© Fastvalue",
					url: "https://cdn.estatemanner.com/tile/qhsdd/{z}/{x}/{y}.png",
					type: "overlay"
				},
        {
					name: "Bản đồ quy hoạch lộ giới",
					visible: false,
					attribution: "© Fastvalue",
					url: "https://cdn.estatemanner.com/tile/qhlg/{z}/{x}/{y}.png",
					type: "overlay"
				}
			],
			search_address: "",
			url:
				"https://mts0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}&s=Gal&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff",
			attribution: "",
			place: "",
			provinces: [],
			places: [],
			currentPlace: null,
			markerLatLng: [10.964112, 106.856461],
			map: {
				center: [10.964112, 106.856461],
				zoom: 17
			},
			imageMap: true,
			render_map: 3232102,
			strPlaceHolder: "Nhập địa điểm, vị trí hoặc tọa độ",
			geo_data: null,
			modalGeoInfo: false,
			duong:'',
			phuongxa:'',
			quanhuyen:'',
			tinhthanh:'',
			phuongxa_code:'',
			quanhuyen_code:'',
			tinhthanh_code:'',
			listCity: [],
			listDistrict: [],
			listWard: [],
			emCityCode: '',
			emDistrictCode: '',
			emWardCode: '',
			emSoToCode: '',
			emSoThuaCode: '',
			isOpenLoading: false,
			runProgress: true,
            resizeWidth: '95vw',
			seg_data: null,
		};
	},
    watch: {
        navExp (newnavExp, oldnavExp) {
        // Our fancy notification (2).
        // console.log('đổi store từ '+oldnavExp+' thành '+newnavExp)
            if (newnavExp == false) {
                this.resizeWidth = '85vw'
            } else {
                this.resizeWidth = '95vw'
            }
        },
    },
	computed: {
		options() {
			return {
				onEachFeature: this.onEachFeatureFunction
			};
		},
		onEachFeatureFunction() {
			return (feature, layer) => {
				// // console.log('jhjhjhjjhjhj',this.map.zoom)
				let final_length = feature.properties.length.toFixed(1)
				// // console.log('final_length',final_length.slice(final_length.indexOf('.') + 1))
				//xử lý font
				let m_font_size = '16px'
				let m_font_color = '#00007c'
				let m_font_weight = 'bold'
				if (this.map.zoom >= 17  && this.map.zoom <  18) {
					m_font_size = '14px'
				}
				if (this.map.zoom >= 18  && this.map.zoom <  19) {
					m_font_size = '13px'
				}
				if (this.map.zoom >= 19  && this.map.zoom <  20) {
					m_font_size = '12px'
				}
				if (this.map.zoom >= 20  && this.map.zoom <  21) {
					m_font_size = '11px'
				}


				if (final_length.slice(final_length.indexOf('.') + 1) == '0'){
					final_length = feature.properties.length.toFixed(0)
				}
				if ( (this.map.zoom >= 17 && this.map.zoom < 18 && feature.properties.length > 60) && (!layer.getTooltip()) ) {
					// // console.log('vô vẽ nè 1',feature.properties.length.toFixed(1))
					layer.bindTooltip(
						"<div class='test' style='transform: rotate("+feature.angle+"deg); padding-top: 20px; font-size: "+m_font_size+"; font-weight: "+m_font_weight+"; color: "+m_font_color+"'>" +
							final_length + ' m' +
								"</div>",
									{ interactive: true, direction: 'center',
						permanent: true,
						sticky: false,
						offset: [0, 0],
						className: 'leaflet-tooltip-own'  }
					); 
				} else if (layer.getTooltip() && feature.properties.length < 60 ){
                    //// console.log('remove tooltip');
                    layer.unbindTooltip()
                }
				if ((this.map.zoom >= 18 && this.map.zoom < 19 && feature.properties.length > 40) && (!layer.getTooltip()) ) {
					// // console.log('vô vẽ nè 2',feature.properties.length.toFixed(1))
					layer.bindTooltip(
						"<div class='test' style='transform: rotate("+feature.angle+"deg); padding-top: 25px; font-size: "+m_font_size+"; font-weight: "+m_font_weight+"; color: "+m_font_color+"'>" +
							final_length + ' m' +
								"</div>",
									{ interactive: true, direction: 'center',
						permanent: true,
						sticky: false,
						offset: [0, 0],
						className: 'leaflet-tooltip-own'  }
					); 
				} else if (layer.getTooltip() && feature.properties.length < 40 ){
                    //// console.log('remove tooltip');
                    layer.unbindTooltip()
                }
				if ((this.map.zoom >= 19 && this.map.zoom < 20 && feature.properties.length > 20) && (!layer.getTooltip()) ) {
					// // console.log('vô vẽ nè 3',feature.properties.length.toFixed(1))
					layer.bindTooltip(
						"<div class='test' style='transform: rotate("+feature.angle+"deg); padding-top: 30px; font-size: "+m_font_size+"; font-weight: "+m_font_weight+"; color: "+m_font_color+"'>" +
							final_length + ' m' +
								"</div>",
									{ interactive: true, direction: 'center',
						permanent: true,
						sticky: false,
						offset: [0, 0],
						className: 'leaflet-tooltip-own'  }
					); 
				} else if (layer.getTooltip() && feature.properties.length < 20 ){
                    //// console.log('remove tooltip');
                    layer.unbindTooltip()
                }
				if ( (this.map.zoom >= 20 && this.map.zoom < 21 && feature.properties.length > 0) && (!layer.getTooltip()) ) {
					// // console.log('vô vẽ nè 4',feature.properties.length.toFixed(1))
					layer.bindTooltip(
						"<div class='test' style='transform: rotate("+feature.angle+"deg); padding-top: 35px; font-size: "+m_font_size+"; font-weight: "+m_font_weight+"; color: "+m_font_color+"'>" +
							final_length + ' m' +
								"</div>",
									{ interactive: true, direction: 'center',
						permanent: true,
						sticky: false,
						offset: [0, 0],
						className: 'leaflet-tooltip-own'  }
					); 
				} else if (layer.getTooltip() && feature.properties.length < 0 ){
                    //// console.log('remove tooltip');
                    layer.unbindTooltip()
                }
			};
		},
        navExp() {
            // console.log('store',this.$store.getters.navExp)
            return this.$store.getters.navExp
        },
		disabledButton () {
			// console.log('vô vô')
			if ((this.emSoToCode || this.emSoThuaCode) && (this.emCityCode && this.emDistrictCode && this.emWardCode)) {
				return false
			} else return true
		},
		optionsProvince () {
			// // console.log('list tỉnh', {
			// 	data: this.listCity,
			// 	id: 'code',
			// 	key: 'name_with_type'
			// })
			return {
				data: this.listCity,
				id: 'code',
				key: 'name_with_type'
			}
		},
		optionsDistrict () {
			// // console.log('list huyện', {
			// 	data: this.listDistrict,
			// 	id: 'code',
			// 	key: 'name_with_type'
			// })
			return {
				data: this.listDistrict,
				id: 'code',
				key: 'name_with_type'
			}
		},
		optionsWard () {
			// // console.log('list xã', {
			// 	data: this.listWard,
			// 	id: 'code',
			// 	key: 'name_with_type'
			// })
			return {
				data: this.listWard,
				id: 'code',
				key: 'name_with_type'
			}
		},
	},
	async mounted() {
		this.search_address = this.address;
		await this.$gmapApiPromiseLazy();
		if (this.center_map !== "" && this.center_map) {
			this.map.center = [
				this.center_map.split(",")[0],
				this.center_map.split(",")[1]
			];
			this.markerLatLng = [
				this.center_map.split(",")[0],
				this.center_map.split(",")[1]
			];
		} else {
			await this.initMap(this.address, "address");
		}
		// // console.log('cityJSON', cityJson)
		var result = [];

		for(var i in cityJson)
			result.push(cityJson [i])
		this.listCity = result
		// console.log('city array', this.listCity)

        if (this.$store.getters.navExp == false) {
            this.resizeWidth = '84vw'
        } else {
            this.resizeWidth = '95vw'
        }
	},
	methods: {
		updateZoom(zoom) {
			this.map.zoom = zoom
			this.seg_key++
		},
		changeProvince (code) {
			this.listDistrict =  []
			this.emDistrictCode = ''
			this.listWard = []
			this.emWardCode = ''
			const districtJson = require('@/assets/json/phuluc_dmhc/district/'+code+'.json')
			for(var i in districtJson)
				this.listDistrict.push(districtJson [i])
			// console.log('list huyện', this.listDistrict)
			// console.log('tỉnh chọn', this.emCityCode)
		},
		changeDistrict (code) {
			this.listWard = []
			this.emWardCode = ''
			const wardJson = require('@/assets/json/phuluc_dmhc/ward/'+code+'.json')
			for(var i in wardJson)
				this.listWard.push(wardJson [i])
			// console.log('list xã', this.listWard)
		},
		getEmCode() {
			if (this.tinhthanh){
				if (this.listCity.length > 0) {
					let checkcotinh = 0
					for (let i = 0; i < this.listCity.length; i++) {
						let e = this.listCity[i]
						if (e.name_with_type.toLowerCase()  == this.tinhthanh.toLowerCase() || e.name.toLowerCase() == this.tinhthanh.toLowerCase()) {
							// console.log('dính chưởng tỉnh thành', e)
							this.emCityCode = e.code
							if (this.quanhuyen) {
								const districtJson = require('@/assets/json/phuluc_dmhc/district/'+this.emCityCode+'.json')
								// // console.log('list district', listDistrict)
								if (districtJson) {
									for(var i in districtJson)
										this.listDistrict.push(districtJson [i])
									
									if (this.listDistrict.length > 0) {
										let checkcohuyen = 0
										for (let i = 0; i < this.listDistrict.length; i++) {
											let q = this.listDistrict[i]
											if (q.name_with_type.toLowerCase()  == this.quanhuyen.toLowerCase() || q.name.toLowerCase() == this.quanhuyen.toLowerCase()) {
												// console.log('dính chưởng quận huyện', q)
												this.emDistrictCode = q.code
												if (this.phuongxa) {
													// console.log('vô phường xã')
													let split1 = this.phuongxa.split(' ')
													// console.log('cắt', split1)
													if (split1[1] == '1' || split1[1] == '2' || split1[1] == '3' || split1[1] == '4' || split1[1] == '5' || split1[1] == '6' || split1[1] == '7' || split1[1] == '8' || split1[1] == '9') {
														this.phuongxa = split1[0]+' 0'+split1[1]
													}
													const wardJson = require('@/assets/json/phuluc_dmhc/ward/'+this.emDistrictCode+'.json')
													if (wardJson) {
														for(var i in wardJson)
															this.listWard.push(wardJson [i])

														if (this.listWard.length > 0) {
															let checkcoxa = 0
															for (let i = 0; i < this.listWard.length; i++) {
																let x = this.listWard[i]
																// console.log('x',this.phuongxa)
																if (x.name_with_type.toLowerCase()  == this.phuongxa.toLowerCase() || x.name.toLowerCase() == this.phuongxa.toLowerCase()) {
																	// console.log('dính chưởng phường xã', x)
																	this.emWardCode = x.code
																}
															}
															if (checkcoxa == 0) {
																return
															}
														}
													}
												}
											}
										}
										if (checkcohuyen == 0) {
											return
										}
									}
								}
							}
						}
					}
					if (checkcotinh == 0) {
						return
					}
				}
			} else {
				if (this.quanhuyen_code || this.phuongxa_code) {

				}
			}
		},
		inscreaseProgress(progress){
			// console.log('gọi tăng')
			progress = progress + 1
			this.progress = progress
			if (this.progress < 100 && this.runProgress == true) {
				let that = this
				setTimeout( function() {
					that.inscreaseProgress(progress)
				}, 30)
			}
		},
		async searchByToThua(){
			this.modalGeoInfo = false
			this.progress = 0
			this.runProgress = true
			this.isOpenLoading = true
			this.inscreaseProgress(this.progress)
			await this.getEmCode()
			if ((this.emSoToCode || this.emSoThuaCode) && (this.emCityCode && this.emDistrictCode && this.emWardCode)) {
				// console.log('đầy đủ thông tin')
				const APItoken = await this.getToken();
				if (APItoken){
					let formdata = {
					token: APItoken,
					land_plot: this.emSoThuaCode,
					land_sheet: this.emSoToCode,
					city_code: this.emCityCode,
					district_code: this.emDistrictCode,
					ward_code: this.emWardCode
					}
					await File.getInfoByLand({ data: formdata }).then((response) => {
					// // console.log('response result',response)
					let datafinal = response.data.data
					//   let that = this
					// // console.log('data final', datafinal)
					if (datafinal.message != 'Hệ thống đang có lỗi xảy ra, vui lòng thử lại sau' && datafinal.message != 'Không tìm thấy thông tin quy hoạch') {
						this.dataResult = datafinal.data
						this.geo_data = this.dataResult  ? this.dataResult.geo_data :  []
						//xử lý tọa độ
						this.seg_data = this.dataResult  ? this.dataResult.seg_data :  []
						// console.log('data seg', this.seg_data)
						for (let i = 0; i < this.seg_data.length; i++) {
							let item = this.seg_data[i].geometry.coordinates
							let f2 = item[0][0], l2 = item[0][1], f1 = item[1][0], l1 = item[1][1];
							let toRadian = Math.PI / 180;
							let y = Math.sin((l2 - l1) * toRadian) * Math.cos(f2 * toRadian);
							let x = Math.cos(f1 * toRadian) * Math.sin(f2 * toRadian) - Math.sin(f1 * toRadian) * Math.cos(f2 * toRadian) * Math.cos((l2 - l1) * toRadian);
							let brng = Math.atan2(y, x) * (180 / Math.PI);
							brng += brng < 0 ? 360 : 0;
							this.seg_data[i].brng = brng
							if ((brng < 360 && brng + 20 > 360) || (brng > 100 && brng < 110)) {
								this.seg_data[i].angle = (brng - 10).toFixed(0)
							} else if ((brng >= 79 && brng < 90)) {
								this.seg_data[i].angle = (brng + 10).toFixed(0)
							} else if ((brng >= 180 && brng < 190)) {
								this.seg_data[i].angle = (brng + 180).toFixed(0)
							} else if ((brng > 180 && brng < 190)) {
								this.seg_data[i].angle = (brng+180).toFixed(0)
							} else if ((brng > 90 && brng < 100) || (brng >= 0 && brng < 10) || (brng > 270 && brng < 280)|| (brng > 80 && brng < 90) || (brng > 260 && brng < 270)) {
								this.seg_data[i].angle = brng.toFixed(0)
							} else if (brng < 180 && brng + 20 > 170 && brng + 20 < 180) {
								this.seg_data[i].angle = (brng + 140).toFixed(0)
							} else if (brng < 180 && brng + 20 > 160 && brng + 20 < 170) {
								this.seg_data[i].angle = (brng + 150).toFixed(0)
							} else if (brng < 180 && brng + 50 > 170 && brng + 50 < 180) {
								this.seg_data[i].angle = (brng - 20).toFixed(0)
							}  else if (brng < 180 && brng + 20 < 170) {
								this.seg_data[i].angle = (brng + 20).toFixed(0)
							} else if (brng < 180 && brng + 20 > 180) {
								this.seg_data[i].angle = (brng + 170).toFixed(0)
							} else if ((brng > 270 && brng + 60 > 360) || (brng > 130 &&  brng < 140)) {
								this.seg_data[i].angle = (brng - 30).toFixed(0)
							} else {
								this.seg_data[i].angle = (brng - 160).toFixed(0)
							}
						}

						this.modalGeoInfo = true
						this.isOpen  = false
						this.map.center = [
							this.geo_data.geometry.coordinates[0][0][0][1] ? this.geo_data.geometry.coordinates[0][0][0][1] : this.geo_data.geometry.coordinates[0][0][1],
							this.geo_data.geometry.coordinates[0][0][0][0] ? this.geo_data.geometry.coordinates[0][0][0][0] : this.geo_data.geometry.coordinates[0][0][0],
						]
						this.markerLatLng = [
							this.geo_data.geometry.coordinates[0][0][0][1] ? this.geo_data.geometry.coordinates[0][0][0][1] : this.geo_data.geometry.coordinates[0][0][1],
							this.geo_data.geometry.coordinates[0][0][0][0] ? this.geo_data.geometry.coordinates[0][0][0][0] : this.geo_data.geometry.coordinates[0][0][0],
						]
						this.isOpenLoading = false
						this.progress = 0
						this.runProgress = false
						// // console.log('quan huyen', this.geo_data.geometry.coordinates[0][0])
					} else {
						this.$toast.open({
						message: 'Không tìm thấy dữ liệu quy hoạch',
						type: 'error',
						position: 'top-right',
						duration: 3000
						})
						this.dataResult = []
						this.geo_data = []
						this.modalGeoInfo = false
						this.isOpen  = false
						this.isOpenLoading = false
						this.progress = 0
						this.runProgress = false
					}
					})
				}
			}
		},
		// changeSearchToThua(event){
		// 	// console.log('event', this.serch_to_thua)
		// 	// this.search_to_thua = true
		// },
    async getToken () {
      // const uninterceptedAxiosInstance = axios.create();
      //   const body = {
      //   client_id: 'BWflWM57LHSivze237MRNsOQxb23DUQ6',
      //   client_secret: 'K9I1955xyA_uQsiei0ucoXAUyO0rnXGz_Cvxx40ZqUOtvcEP0hZaz4pHGSHYIwql'
      //   }; // request JSON body
      //   const headers = { 'Content-type': 'application/json','Access-Control-Allow-Origin': '*', 'Access-Control-Allow-Credentials':true }; // auth header with bearer token
      // const response = await uninterceptedAxiosInstance.post('https://app.estatemanner.com/api/v1/auth/credentials', body,{headers}).catch(function (error) {
      //     if (error.response) {
      //       // console.log(error.response.data);
      //       // console.log(error.response.status);
      //       // console.log(error.response.headers);
      //     }
      //   })
      // // console.log('data token', response.data.data.access_token)
      // if (response.data.data.access_token) {
      //   return response.data.data.access_token
      // } else {
      //   return null
      // }
      // let formData = {
      //   client_id: 'BWflWM57LHSivze237MRNsOQxb23DUQ6',
      //   client_secret: 'K9I1955xyA_uQsiei0ucoXAUyO0rnXGz_Cvxx40ZqUOtvcEP0hZaz4pHGSHYIwql'
      // }
      let token = ''
      await File.getToken().then((response) => {
				// console.log('response token',response)
        token = response.data.data.data.access_token
			})
      return token
    },
    async getInfoByCoord (coordinates) {
		this.modalGeoInfo = false
		this.progress = 0
		this.runProgress  = true
		this.isOpenLoading = true
		this.inscreaseProgress(this.progress)
      const APItoken = await this.getToken();
      // console.log('api token', APItoken)
      if (APItoken){
        // const uninterceptedAxiosInstance = axios.create();
        // const body = { lat: coordinates[0], lng: coordinates[1] }; // request JSON body
        // const headers = { 'Content-type': 'application/json','Access-Control-Allow-Origin': '*', 'Access-Control-Allow-Credentials':true, 'Authorization': `Bearer ${APItoken}` }; // auth header with bearer token
        // uninterceptedAxiosInstance.post('https://app.estatemanner.com/api/v1/map/feature/coord', body, { headers })
        //     .then(response => 
        //     {
        //       // console.log('response', response.data.data)
        //       this.dataResult = response.data.data
        //       this.geo_data = this.dataResult.geo_data
        //       this.modalGeoInfo = true
        //     })
        //     .catch(function (error) {
        //       that = this
        //       if (error.response) {
        //         // console.log(error.response.data);
        //         // console.log(error.response.status);
        //         // console.log(error.response.headers);
        //         that.$toast.open({
        //             message: error.response.data.message,
        //             type: 'error',
        //             position: 'top-right',
        //             duration: 3000
        //           })
        //           that.dataResult = null
        //           that.geo_data = null
        //       }
        //     });
        let formdata = {
          token: APItoken,
          lat: coordinates[0],
          lng: coordinates[1]
        }
        await File.getInfoByCoord({ data: formdata }).then((response) => {
          // console.log('response result',response)
          let datafinal = response.data.data
        //   let that = this
          // console.log('data final', datafinal)
          if (datafinal.message != 'Hệ thống đang có lỗi xảy ra, vui lòng thử lại sau' && datafinal.message != 'Không tìm thấy thông tin quy hoạch') {
            this.dataResult = datafinal.data
            this.geo_data = this.dataResult  ? this.dataResult.geo_data :  []
		
			//xử lý tọa độ
			this.seg_data = this.dataResult  ? this.dataResult.seg_data :  []
			
			for (let i = 0; i < this.seg_data.length; i++) {
				let item = this.seg_data[i].geometry.coordinates
				let f2 = item[0][0], l2 = item[0][1], f1 = item[1][0], l1 = item[1][1];
				let toRadian = Math.PI / 180;
				let y = Math.sin((l2 - l1) * toRadian) * Math.cos(f2 * toRadian);
				let x = Math.cos(f1 * toRadian) * Math.sin(f2 * toRadian) - Math.sin(f1 * toRadian) * Math.cos(f2 * toRadian) * Math.cos((l2 - l1) * toRadian);
				let brng = Math.atan2(y, x) * (180 / Math.PI);
				brng += brng < 0 ? 360 : 0;
				this.seg_data[i].brng = brng
				if ((brng < 360 && brng + 20 > 360) || (brng > 100 && brng < 110)) {
					this.seg_data[i].angle = (brng - 10).toFixed(0)
				} else if ((brng >= 79 && brng < 90)) {
					this.seg_data[i].angle = (brng + 10).toFixed(0)
				} else if ((brng >= 180 && brng < 190)) {
					this.seg_data[i].angle = (brng + 180).toFixed(0)
				}else if ((brng > 90 && brng < 100) || (brng >= 0 && brng < 10) || (brng > 270 && brng < 280)|| (brng > 80 && brng < 90) || (brng > 260 && brng < 270)) {
					this.seg_data[i].angle = brng.toFixed(0)
				}else if (brng < 180 && brng + 20 > 170 && brng + 20 < 180) {
					this.seg_data[i].angle = (brng + 140).toFixed(0)
				} else if (brng < 180 && brng + 20 > 160 && brng + 20 < 170) {
					this.seg_data[i].angle = (brng + 150).toFixed(0)
				} else if (brng < 180 && brng + 50 > 170 && brng + 50 < 180) {
					this.seg_data[i].angle = (brng - 20).toFixed(0)
				}  else if (brng < 180 && brng + 20 < 170) {
					this.seg_data[i].angle = (brng + 20).toFixed(0)
				} else if (brng < 180 && brng + 20 > 180) {
					this.seg_data[i].angle = (brng + 170).toFixed(0)
				} else if ((brng > 270 && brng + 60 > 360) || (brng > 130 &&  brng < 140)) {
					this.seg_data[i].angle = (brng - 30).toFixed(0)
				} else {
					this.seg_data[i].angle = (brng - 160).toFixed(0)
				}
			}

            this.modalGeoInfo = true
			this.phuongxa_code = this.dataResult.attributes.level_3
			this.quanhuyen_code = this.dataResult.attributes.level_2
			// // console.log('quan huyen', this.quanhuyen)
			this.isOpenLoading = false
			this.progress = 0
			this.runProgress = false
          } else {
            this.$toast.open({
              message: 'Không tìm thấy dữ liệu quy hoạch',
              type: 'error',
              position: 'top-right',
              duration: 3000
            })
            this.dataResult = []
            this.geo_data = []
            this.modalGeoInfo = false
			this.isOpenLoading = false
			this.progress = 0
			this.runProgress = false
          }
        })
      }
		},
		geoInfo() {
			this.modalGeoInfo = !this.modalGeoInfo;
			// console.log("open", this.modalGeoInfo);
		},
		closeModalGeoInfo() {
			this.modalGeoInfo = false;
		},
		async handleOpenEM() {
			this.modalGeoInfo = false
			// console.log("mở");
			await this.getEmCode()
			this.isOpen = true;
		},
		closeModal() {
			this.isOpen = false;
		},
		changePlace(event) {
			this.search_address = event.target.value;
		},
		handleView() {
			if (
				this.url ===
				"https://mts0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}&s=Gal&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff"
			) {
				// this.url = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
				// this.attribution = 'Tiles © ; Esri: Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
				this.url =
					"https://mts1.google.com/vt/lyrs=s@186112443&hl=x-local&src=app&x={x}&y={y}&z={z}&s=Galile&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff";
				this.imageMap = false;
			} else {
				this.url =
					"https://mts0.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}&s=Gal&apistyle=s.t%3A2|s.e%3Al|p.v%3Aoff";
				this.attribution = "© Fastvalue";
				this.imageMap = true;
			}
		},
		async initMap(address, type) {
			// console.log('vô 3')
			// eslint-disable-next-line no-undef
			const geocoder = new google.maps.Geocoder();
			await this.geocodeAddress(geocoder, address, type);
		},
		async geocodeAddress(geocoder, address, type) {
			// console.log('vô 4')
			let center = {};
			let duong = ''
			let phuongxa = ''
			let quanhuyen = ''
			let tinhthanh = ''
			// let address = this.address
			if (!address) {
				address = document.getElementById("coordinate").value;
			}
			if (address) {
				let keySearch = {};
				if (type === "address") {
					keySearch = {
						address: address
					};
				} else if (type === "location") {
					keySearch = {
						location: address
					};
				}
				await geocoder.geocode(keySearch, function(results, status) {
					if (status === "OK") {
						// console.log('ket qua',results[0])
						const marker = {
							position: results[0].geometry.location
						};
						center = [
							parseFloat(marker.position.lat()),
							parseFloat(marker.position.lng())
						];
						if (results[0].address_components[results[0].address_components.length - 1].long_name.toLowerCase() == 'việt nam' ){
							if (results[0].address_components.length == 5) {
								duong = results[0].address_components[0].long_name
								phuongxa = results[0].address_components[1].long_name
								quanhuyen = results[0].address_components[2].long_name
								tinhthanh = results[0].address_components[3].long_name
							} else if (results[0].address_components.length == 4) {
								phuongxa = results[0].address_components[0].long_name
								quanhuyen = results[0].address_components[1].long_name
								tinhthanh = results[0].address_components[2].long_name
							} else if (results[0].address_components.length == 3) {
								quanhuyen = results[0].address_components[0].long_name
								tinhthanh = results[0].address_components[1].long_name
							} else if (results[0].address_components.length == 2){
								tinhthanh = results[0].address_components[0].long_name
							}
						} else {
							if (results[0].address_components.length == 4) {
								duong = results[0].address_components[0].long_name
								phuongxa = results[0].address_components[1].long_name
								quanhuyen = results[0].address_components[2].long_name
								tinhthanh = results[0].address_components[3].long_name
							} else if (results[0].address_components.length == 3) {
							phuongxa = results[0].address_components[0].long_name
							quanhuyen = results[0].address_components[1].long_name
							tinhthanh = results[0].address_components[2].long_name
							} else if (results[0].address_components.length == 2) {
								quanhuyen = results[0].address_components[0].long_name
								tinhthanh = results[0].address_components[1].long_name
							} else {
								tinhthanh = results[0].address_components[0].long_name
							}
						}
						
						 
						// console.log('ketqua dia chi',results[0].address_components)
						// console.log('ketqua phuong xa',phuongxa)
						// console.log('ketqua quan huyen',quanhuyen)
						// console.log('ketqua tinh thanh',tinhthanh)
					} else {
						// alert('Geocode was not successful for the following reason: ' + status)
					}
				});
				this.map.center = center;
				this.markerLatLng = center;
				this.duong = duong;
				this.phuongxa = phuongxa;
				this.quanhuyen = quanhuyen;
				this.tinhthanh = tinhthanh;
				// console.log('quan huyen', this.quanhuyen)
        // console.log('gọi vô')
        // this.getInfoByCoord(this.markerLatLng)
			}
		},
		choosePoint(event) {
			// // console.log('choosePoint', event)
			this.map.center = [event.latlng.lat, event.latlng.lng];
			this.markerLatLng = [event.latlng.lat, event.latlng.lng];
			this.search_address = event.latlng.lat + "," + event.latlng.lng;

			// this.geo_data = this.dataResult.geo_data;
      this.getInfoByCoord(this.markerLatLng);
			// let latlng = {
			//   lat: event.latlng.lat,
			//   lng: event.latlng.lng
			// }
			// this.initMap(latlng, 'location')
		},
		setPlace(place) {
			if (place.geometry && place.geometry.location) {
				// console.log('vô I')
				this.search_address = place.formatted_address;
				this.currentPlace = place;
				this.map.center = [
					place.geometry.location.lat(),
					place.geometry.location.lng()
				];
				this.markerLatLng = [
					place.geometry.location.lat(),
					place.geometry.location.lng()
				];
				this.initMap(this.search_address, "address");
        this.getInfoByCoord(this.markerLatLng)
			} else {
				// console.log('vô II')
				if (place.name) {
					// console.log('vô II.1')
					let location = place.name;
					this.search_address = place.name;
					if (
						location.split(",") &&
						location.split(",").length === 2 &&
						parseFloat(location.split(",")[0]) &&
						parseFloat(location.split(",")[1])
					) {
						// console.log('vô II.1.1')
						let lat = parseFloat(location.split(",")[0]);
						let lng = parseFloat(location.split(",")[1]);
						this.map.center = [lat, lng];
						this.markerLatLng = [lat, lng];
            this.getInfoByCoord(this.markerLatLng)
					} else {
						// console.log('vô II.1.2')
						this.initMap(location, "address");
					}
				} else {
					// console.log('vô II.2')
					this.initMap(this.search_address, "address");
				}
			}
		},
		handleCancel(event) {
			this.$emit("cancel", event);
		},
		handleAction(event) {
			const data = this.markerLatLng[0] + "," + this.markerLatLng[1];
			this.$emit("cancel", event);
			this.$emit("action", data);
		},
		handleDelete() {
			this.search_address = "";
		},
		validateBeforeSubmit() {
			const isValid = this.$refs.observer.validate();
			if (isValid) {
				this.handleAction();
			}
		},
		geoLocate() {
			navigator.geolocation.getCurrentPosition(position => {
				this.map.center = [position.coords.latitude, position.coords.longitude];
				this.markerLatLng = [
					position.coords.latitude,
					position.coords.longitude
				];
        this.getInfoByCoord(this.markerLatLng)
			});
		},
		handleSearch() {
      
			if (this.search_address) {
				// console.log('vô 1')
				let location = this.search_address;
				if (
					location.split(",") &&
					location.split(",").length === 2 &&
					parseFloat(location.split(",")[0]) &&
					parseFloat(location.split(",")[1])
				) {
					// console.log('vô 1.1')
					let lat = parseFloat(location.split(",")[0]);
					let lng = parseFloat(location.split(",")[1]);
					this.map.center = [lat, lng];
					this.markerLatLng = [lat, lng];
          this.getInfoByCoord(this.markerLatLng)
				} else {
					// console.log('vô 1.2')
					this.initMap(location, "address");
				}
			} else {
				// console.log('vô 2')
				this.initMap(this.search_address, "address");
			}
		}
	},
	beforeMount() {}
};
</script>
<style lang="scss" scoped>
    @keyframes fade {
	0%   { transform: scale(1,1)      translateY(0); }
	10%  { transform: scale(1.1,.9)   translateY(0); }
	30%  { transform: scale(.9,1.1)   translateY(-15px); }
	50%  { transform: scale(1.05,.95) translateY(0); }
	57%  { transform: scale(1,1)      translateY(-5px); }
	64%  { transform: scale(1,1)      translateY(0); }
	100% { transform: scale(1,1)      translateY(0); }
}

/deep/ .leaflet-tooltip {
	background: none !important;;
	border: none !important;
	box-shadow: none !important;
}
.modal-delete {
	// position: fixed;
    position: relative;
    width: fit-content;
	z-index: 2;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	// padding-top: 10px;
    margin-top: 5px;
	// background: rgba(0, 0, 0, 0.6);
	.card {
		max-width: 95vw;
		width: 100%;
		height: 90vh;
		margin-bottom: 0;
		@media (max-width: 767px) {
			max-width: 100dvh;
			height: 100dvh;
		}
		&-header {
			border-bottom: 1px solid #dddddd;
			h3 {
				color: #333333;
			}
			img {
				cursor: pointer;
			}
		}
		&-body {
			text-align: center;
			padding: 40px;
			p {
				color: #333333;
				margin-bottom: 40px;
			}

			.btn__group {
				.btn {
					max-width: 150px;
					width: 100%;
					margin: 0 10px;
				}
			}
		}
		//.g-map{
		//  width: 90vw;
		//  height: 80vh;
		//  border-radius: 5px;
		//  @media (max-width: 1023px) {
		//    width: 100vw;
		//  }
		//  @media (max-width: 767px) {
		//    max-width: 100vw;
		//    height: 100dvh;
		//  }
		//}
	}
}
.input-map {
	box-sizing: border-box;
	margin-right: 10px;
	width: 100%;
	//padding: 0 10px;
}
.btn {
	&-search {
		height: 40px;
		background: #faa831;
		color: #ffffff;
		font-weight: 700;
		white-space: nowrap;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
		margin-right: 10px;
		margin-left: 10px;
		&:hover {
			background: #f8b24b;
		}
		span {
			margin-right: 5px;
		}
		@media (max-width: 767px) {
			width: 100%;
			margin-top: 10px;
			margin-left: 0;
		}
	}
	&-cancel {
		height: 40px;
		background: #ffffff;
		font-weight: 700;
		white-space: nowrap;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
		margin-right: 10px !important;
		border-radius: 3px;
	}
	&-location {
		min-width: auto;
	}
	&-exit {
		cursor: pointer;
		margin: 10px;
		width: 20px;
		height: auto;
	}
}
.main-map {
	position: relative;
	height: 100%;
	width: 95vw;
	transition-timing-function: ease;
	transition-duration: 0.25s;
	overflow-x: hidden;
	@media (max-width: 1023px) {
		width: 100%;
	}
	.layer-map {
		position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		z-index: 0;
		transition-timing-function: ease;
		transition-duration: 0.25s;
	}
}
.icon_marker {
	width: 25px;
}
.mini_btn {
	width: 30px;
	height: 30px;
	padding: unset !important;
}
.search-container {
	position: relative;
	margin-right: 10px;
	.icon-container {
		cursor: pointer;
		position: absolute;
		right: 0;
		top: 50%;
		width: 20px;
		height: auto;
		transform: translateY(-50%);
		margin-right: 20px;
		img {
			width: 100%;
		}
	}
}
.btn-map {
	background: #ffffff;
	border-radius: 5px;
	border: 3px solid #ffffff;
	padding: 0;
	box-sizing: border-box;
	img {
		max-width: 50px;
		height: auto;
	}
}
.modal-detail {
  position: fixed;
  z-index: 1031;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,.6);
  .card {
	height: auto;
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    max-width: 500px;
    width: 100%;
    max-height: 62vh;
    margin-bottom: 0;
    padding: 35px 95px;
    @media (max-width: 787px) {
      padding: 20px 10px;
    }
    &-header {
      border-bottom: 1px solid #DDDDDD;
      h3 {
        color: #333333;
      }
      img {
        cursor: pointer;
      }
    }
    &-body {
      text-align: center;
      p {
        color: #333333;
        margin-bottom: 40px;
      }

      .btn__group {
        .btn {
          max-width: 150px;
          width: 100%;
          margin: 0 10px;
        }
      }
    }
  }
}

.card{
  .contain-detail{
    overflow-y: auto;
    overflow-x: hidden;
    margin-top: 20px;
    &::-webkit-scrollbar{
      width: 2px;
    }
  }
  &-title{
    background: #F3F2F7;
    padding: 16px 20px;
    margin-bottom: 0;
    .title{
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 0;
    }
  }
  &-table{
    border-radius: 5px;
    background: #FFFFFF;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    width: 99%;
    margin: 50px auto 50px;
  }
  &-body{
    padding: 35px 30px 40px;
  }
  &-info{
    .title{
      font-size: 1.125rem;
      font-weight: 700;
      margin-top: 28px;
    }
  }
  &-land{
    position: relative;
    padding: 0;
  }
}

.container-title{
  margin: -35px -95px auto;
  padding: 35px 95px 0;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  .title{
    font-size: 1.2rem;
    @media (max-width: 767px) {
      font-size: 1.125rem;
    }
  }
  &__footer{
    margin: auto -95px -35px;
    padding: 20px 95px 20px;
    @media (max-width: 767px) {
      .btn-white{
        margin-bottom: 20px;
      }
    }
  }
}

.title{
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: 25px;
  color: #000000;
}



</style>
