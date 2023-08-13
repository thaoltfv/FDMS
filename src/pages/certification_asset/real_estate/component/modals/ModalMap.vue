<template>
	<div class="modal-delete d-flex justify-content-center align-items-center">
		<!-- <div class="" v-if="isOpen" @click.self="closeModal" style="position: absolute;
    z-index: 999;
    top: 150px;
    left: 75px;">
      <div class="">
        <iframe :src="url_modal" frameborder="0" width="1535vw" height="530vh"></iframe>
        <button class="" @click="closeModal" style="position: absolute;
    width: 50px;
    height: 50px;
    top: 7px;
    right: 6px;
    border-radius: 30px;
    background-color: white;
    border: 0px;"><img 
             src="@/assets/images/icon-btn-back.svg"
             alt="icon"></button>
      </div>
    </div> -->
		<ValidationObserver
			tag="form"
			ref="observer"
			@submit.prevent="validateBeforeSubmit"
		>
			<div class="card" style="">
				<div
					v-if="modalGeoInfo"
					class="card"
					style="top: 9vh;position: absolute; overflow: scroll;
    z-index: 999999;transform: none; transition: transform 525ms cubic-bezier(0, 0, 0.2, 1) 5ms;width: 27%;
    height: -webkit-fill-available;"
				>
					<button
						class=""
						@click="closeModalGeoInfo"
						style="position: absolute;
    right: 0;
    border-radius: 30px;
    background-color: white;
    border: 0px;"
					>
						<img src="@/assets/images/icon-btn-back.svg" alt="icon" />
					</button>

					<div class="row" style="margin: 0;">
						<div class="col-12">
							<h2
								style="margin: 0px;
    line-height: 1.6;
    color: rgb(9, 44, 76);"
							>
								Thông tin chi tiết
							</h2>
						</div>
						<div v-if="!dataResult" class="row">
							<h3>Không tìm thấy thông tin</h3>
						</div>
						<div v-else class="row" style="margin: 0;">
							<div class="col-12">
								<h3 style="color: rgb(9, 44, 76);">Bản vẽ kỹ thuật</h3>
							</div>
							<div class="col-5">
								<span>Số tờ</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.so_to_ban_do ? dataResult.attributes.so_to_ban_do : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Số thửa</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.so_hieu_thua ? dataResult.attributes.so_hieu_thua : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Số nhà</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.so_nha ? dataResult.attributes.so_nha : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Đường</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.duong ? dataResult.attributes.duong : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Phường</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.phuong ? dataResult.attributes.phuong : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Quận</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.quan ? dataResult.attributes.quan : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Tọa độ Google Map</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.wgs84 ? dataResult.attributes.wgs84 : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Tọa độ địa chính</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.vn2000 ? dataResult.attributes.vn2000 : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Mục đích sử dụng đất</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.mdsdd ? dataResult.attributes.mdsdd : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Diện tích tổng (m2)</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.dien_tich_thua ? dataResult.attributes.dien_tich_thua : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Diện tích nằm trong lộ giới (m2)</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.dt_k_phu_hop ? dataResult.attributes.dt_k_phu_hop : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Loại đất quy hoạch</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.loai_dat_quy_hoach ? dataResult.attributes.loai_dat_quy_hoach : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Số mặt tiếp giáp</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.tong_mat_tien ? dataResult.attributes.tong_mat_tien : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Phân cấp</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.tiep_giap_1 ? dataResult.attributes.tiep_giap_1 : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Chiều dài</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.chieu_sau ? dataResult.attributes.chieu_sau : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Chiều rộng</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.mat_tien ? dataResult.attributes.mat_tien : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Độ rộng đường nhỏ nhất</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.rong_hem_nho_1 ? dataResult.attributes.rong_hem_nho_1 : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Độ rộng đường trước nhà</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.hem_truoc_nha_1 ? dataResult.attributes.hem_truoc_nha_1 : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Khoảng cách tới đường chính</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.khoang_cach_1 ? dataResult.attributes.khoang_cach_1 : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Hướng</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.huong ? dataResult.attributes.huong : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Hình dáng</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.nhan_dien ? dataResult.attributes.nhan_dien : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Quyết định</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.quyet_dinh ? dataResult.attributes.quyet_dinh : 'Không có thông tin'}}</span>
							</div>
							<div
								class="col-12"
								style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"
							></div>
							<div class="col-5">
								<span>Đồ án</span>
							</div>
							<div class="col-7">
								<span>{{ dataResult.attributes.do_an ? dataResult.attributes.do_an : 'Không có thông tin'}}</span>
							</div>
							<!-- <div class="col-12" style="border-bottom: 1px solid rgba(110,117,130,0.2);margin-bottom: 5px;"></div> -->
						</div>
					</div>
				</div>
				<div class="d-block d-sm-flex justify-content-between my-3">
					<div class="search-container w-100 d-flex">
						<gmap-autocomplete
							:value="search_address"
							:placeholder="strPlaceHolder"
							@place_changed="setPlace"
							@change="changePlace"
							@keyup.enter="changePlace"
							class="input-map"
							:options="{
								fields: ['geometry', 'address_components', 'formatted_address'],
								componentRestrictions: { country: 'vn' }
							}"
						/>
						<div class="icon-container" @click="handleSearch">
							<img src="@/assets/icons/ic_search.svg" alt="" />
						</div>
					</div>
					<!-- <button class="btn btn-search" type="button" @click="handleOpenEM" style="background-color: #FFFFFF;">
				<svg width="25" height="25" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z"
                    fill="#007EC6"/>
                </svg>
			</button> -->
					<input type="text" id="coordinate" :value="address" class="d-none" />
					<button class="btn btn-search" type="button" @click="handleAction">
						Xác nhận
					</button>
					<button
						class="btn btn-white btn-cancel"
						type="button"
						@click="handleCancel"
					>
						Trở lại
					</button>
				</div>
				<div class="main-map">
					<div id="mapid" class="layer-map">
						<l-map
							ref="lmap"
							style="height: 100%;"
							:zoom="map.zoom"
							:center="map.center"
							:maxZoom="20"
							:options="{ zoomControl: false }"
							@click="choosePoint($event)"
						>
							<l-tile-layer
								:url="url"
								:attribution="attribution"
								:options="{ maxNativeZoom: 20, maxZoom: 20 }"
							></l-tile-layer>
							<l-tile-layer
								v-for="tileProvider in tileProviders"
								:key="tileProvider.name"
								:name="tileProvider.name"
								:visible="tileProvider.visible"
								:url="tileProvider.url"
								:attribution="tileProvider.attribution"
								:layer-type="tileProvider.type"
								:options="{ maxNativeZoom: 20, maxZoom: 20 }"
							/>
							<!-- <l-tile-layer
			:url="url_quyhoach"
			:min-zoom="12"
			:options="{ maxNativeZoom: 19, maxZoom: 20}" -->
							/>
							<!-- <l-tile-layer
                          url="https://cdn.estatemanner.com/tile/paper_map/thanh_pho_ha_noi/quan_cau_giay/{z}/{x}/{y}.png"
                          :min-zoom="12"
                          :options="{ maxNativeZoom: 19, maxZoom: 20}"
                        /> -->
							<l-control-zoom position="topright"></l-control-zoom>
							<l-control position="topright">
								<button
									class="btn btn-orange mini_btn"
									type="button"
									@click="geoLocate"
								>
									<img
										src="@/assets/icons/ic_locate_white.svg"
										alt="location"
									/>
								</button>
							</l-control>
							<l-control position="bottomright">
								<button class="btn btn-map" @click="handleView" type="button">
									<img
										v-if="!imageMap"
										src="@/assets/images/im_map.png"
										alt=""
									/>
									<img
										v-if="imageMap"
										src="@/assets/images/im_satellite.png"
										alt=""
									/>
								</button>
							</l-control>
							<l-control-layers position="bottomright"></l-control-layers>
							<l-marker :lat-lng="markerLatLng">
								<l-icon class-name="someExtraClass" :iconAnchor="[30, 58]">
									<img
										style="width: 60px !important"
										class="icon_marker"
										src="@/assets/images/svg_home.svg"
										alt=""
									/>
								</l-icon>
								<l-tooltip>Vị trí tài sản</l-tooltip>
							</l-marker>
							<l-geo-json :geojson="geo_data"></l-geo-json>
							<l-control position="topleft">
								<button v-if="dataResult"
									class="btn btn-orange mini_btn"
									type="button"
									@click="geoInfo"
									style="    border-radius: 50px;
    background: white;"
								>
									<svg
										width="40"
										height="40"
										viewBox="0 0 12 13"
										fill="none"
										xmlns="http://www.w3.org/2000/svg"
									>
										<path
											d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z"
											fill="#007EC6"
										/>
									</svg>
								</button>
							</l-control>
						</l-map>
					</div>
				</div>
			</div>
		</ValidationObserver>
	</div>
</template>
<style lang="scss">
@import "../../../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import "../../../../../../node_modules/leaflet/dist/leaflet.css";
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
	LGeoJson
} from "vue2-leaflet";
import Vue from "vue";
import Icon from "buefy";
import InputText from "@/components/Form/InputText";
import axios from '@/plugins/axios'

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
		InputText,
		LControlLayers,
		LGeoJson
	},
	data() {
		return {
			dataResult: null,
			url_modal: "https://app.estatemanner.com/map",
			isOpen: false,
			url_quyhoach: "",
			tileProviders: [
				{
					name: "Bản đồ ranh tờ, thửa",
					visible: false,
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
				zoom: 15
			},
			imageMap: true,
			render_map: 3232102,
			strPlaceHolder: "Nhập địa điểm, vị trí hoặc tọa độ",
			geo_data: null,
			modalGeoInfo: false,
		};
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
	},
	methods: {
    async getToken () {
      const uninterceptedAxiosInstance = axios.create();
        const body = {
        client_id: 'BWflWM57LHSivze237MRNsOQxb23DUQ6',
        client_secret: 'K9I1955xyA_uQsiei0ucoXAUyO0rnXGz_Cvxx40ZqUOtvcEP0hZaz4pHGSHYIwql'
        }; // request JSON body
        const headers = { 'Access-Control-Allow-Credentials':true }; // auth header with bearer token
      const response = await uninterceptedAxiosInstance.post('https://app.estatemanner.com/api/v1/auth/credentials', body,{headers}).catch(function (error) {
          if (error.response) {
            console.log(error.response.data);
            console.log(error.response.status);
            console.log(error.response.headers);
          }
        })
      console.log('data token', response.data.data.access_token)
      if (response.data.data.access_token) {
        return response.data.data.access_token
      } else {
        return null
      }
    },
    async getInfoByCoord (coordinates) {
      const APItoken = await this.getToken();
      if (APItoken){
        const uninterceptedAxiosInstance = axios.create();
        const body = { lat: coordinates[0], lng: coordinates[1] }; // request JSON body
        const headers = { 'Access-Control-Allow-Credentials':true, 'Authorization': `Bearer ${APItoken}` }; // auth header with bearer token
        uninterceptedAxiosInstance.post('https://app.estatemanner.com/api/v1/map/feature/coord', body, { headers })
            .then(response => 
            {
              console.log('response', response.data.data)
              this.dataResult = response.data.data
              this.geo_data = this.dataResult.geo_data
              this.modalGeoInfo = true
            })
            .catch(function (error) {
              that = this
              if (error.response) {
                console.log(error.response.data);
                console.log(error.response.status);
                console.log(error.response.headers);
                that.$toast.open({
                    message: error.response.data.message,
                    type: 'error',
                    position: 'top-right',
                    duration: 3000
                  })
                  that.dataResult = null
                  that.geo_data = null
              }
            });
      }
		},
		geoInfo() {
			this.modalGeoInfo = !this.modalGeoInfo;
			console.log("open", this.modalGeoInfo);
		},
		closeModalGeoInfo() {
			this.modalGeoInfo = false;
		},
		handleOpenEM() {
			console.log("mở");
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
			// eslint-disable-next-line no-undef
			const geocoder = new google.maps.Geocoder();
			await this.geocodeAddress(geocoder, address, type);
		},
		async geocodeAddress(geocoder, address, type) {
			let center = {};
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
						// console.log(results[0])
						const marker = {
							position: results[0].geometry.location
						};
						center = [
							parseFloat(marker.position.lat()),
							parseFloat(marker.position.lng())
						];
					} else {
						// alert('Geocode was not successful for the following reason: ' + status)
					}
				});
				this.map.center = center;
				this.markerLatLng = center;
        console.log('gọi vô')
        this.getInfoByCoord(this.markerLatLng)
			}
		},
		choosePoint(event) {
			// console.log('choosePoint', event)
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
        this.getInfoByCoord(this.markerLatLng)
			} else {
				if (place.name) {
					let location = place.name;
					this.search_address = place.name;
					if (
						location.split(",") &&
						location.split(",").length === 2 &&
						parseFloat(location.split(",")[0]) &&
						parseFloat(location.split(",")[1])
					) {
						let lat = parseFloat(location.split(",")[0]);
						let lng = parseFloat(location.split(",")[1]);
						this.map.center = [lat, lng];
						this.markerLatLng = [lat, lng];
            this.getInfoByCoord(this.markerLatLng)
					} else {
						this.initMap(location, "address");
					}
				} else {
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
				let location = this.search_address;
				if (
					location.split(",") &&
					location.split(",").length === 2 &&
					parseFloat(location.split(",")[0]) &&
					parseFloat(location.split(",")[1])
				) {
					let lat = parseFloat(location.split(",")[0]);
					let lng = parseFloat(location.split(",")[1]);
					this.map.center = [lat, lng];
					this.markerLatLng = [lat, lng];
          this.getInfoByCoord(this.markerLatLng)
				} else {
					this.initMap(location, "address");
				}
			} else {
				this.initMap(this.search_address, "address");
			}
		}
	},
	beforeMount() {}
};
</script>
<style lang="scss" scoped>
.modal-delete {
	position: fixed;
	z-index: 10002;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	padding-top: 10px;
	background: rgba(0, 0, 0, 0.6);
	.card {
		max-width: 90vw;
		width: 100%;
		height: 80vh;
		margin-bottom: 0;
		@media (max-width: 767px) {
			max-width: 100vh;
			height: 100vh;
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
		//    height: 100vh;
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
	width: 90vw;
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
</style>
