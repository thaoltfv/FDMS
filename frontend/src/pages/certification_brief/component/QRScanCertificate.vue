<template>
	<div class="btn-history" style="">
		<button class="btn btn-orange btn-history" @click="showModal">
			<img src="@/assets/icons/ic_qr_scan.svg" class="mt-n1" alt="history" />
		</button>
		<ModalPrintQRScanCertificate
			v-if="openPrint"
			:certificateId="id"
			:certificateNum="certificate_num"
			:data="data"
			@cancel="openPrint = false"
		/>
	</div>
</template>
<script>
import { ref } from "vue";
import _ from "lodash";
import moment from "moment";
import AppraiserCompany from "@/models/AppraiserCompany";
import ModalPrintQRScanCertificate from "./modals/ModalPrintQRScanCertificate.vue";
export default {
	name: "QRScanCertificate",
	props: {
		id: {
			type: [Number, String],
			default: () => 0,
		},
		certificate_num: {
			type: String,
			default: () => "",
		},
		data: {
			type: Object,
			default: () => {},
		},
	},
	data() {
		return {
			infoCompany: {
				linkImage: "",
				companyName: "",
				base64: "",
			},
			openPrint: false,
		};
	},
	computed: {
		getHistoryTextColor() {
			return this.historyList.map((item) => {
				return this.loadColor(item);
			});
		},
	},
	components: {
		ModalPrintQRScanCertificate,
	},
	beforeMount() {
		// this.getCompanies();
	},
	created() {},
	methods: {
		async getCompanies(id) {
			const resp = await AppraiserCompany.detail();
			const data = resp.data.data;
			if (data.length > 0) {
				this.infoCompany = {
					linkImage: data[0].link,
					companyName: data[0].name,
					// base64: await this.getBase64ImageFromURL(data[0].link),
				};
			}
			// console.log(this.infoCompany);
		},

		getBase64ImageFromURL(url) {
			if (!url) return "";
			return new Promise((resolve, reject) => {
				const img = new Image();
				img.setAttribute("crossOrigin", "anonymous");
				img.onload = () => {
					const canvas = document.createElement("canvas");
					canvas.width = img.width;
					canvas.height = img.height;

					const ctx = canvas.getContext("2d");
					ctx.drawImage(img, 0, 0);

					const dataURL = canvas.toDataURL("image/png");

					resolve(dataURL);
				};

				img.onerror = (error) => {
					reject(error);
				};

				img.src = url + "?r=" + Math.floor(Math.random() * 100000);
			});
		},
		showModal() {
			this.openPrint = true;
		},
		onClose() {
			this.openPrint = false;
		},
		formatDateTime(value) {
			return moment(String(value)).format("HH:mm DD/MM/YYYY");
		},
		loadColor(item) {
			let color = "";
			if (item.log_name == "update_status") {
				if (item.description.includes("từ chối")) color = "text-danger";
				else if (item.description.includes("Hủy")) color = "text-danger";
				else color = "text-success";
			}
			return color;
		},
	},
};
</script>
<style scoped lang="scss">
.card {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 1rem;

	&-footer {
		padding: 15px 24px;
	}

	&-title {
		padding: 20px 25px 10px;
		margin-bottom: 0;
		color: #e8e8e8;
		border-bottom: 2px solid;
		&__img {
			padding: 8px 20px;
		}
		h3 {
			color: #007ec6;
		}
		@media (max-width: 768px) {
			padding: 12px;
		}

		.title {
			font-size: 1.125rem;
			font-weight: 700;
			margin-bottom: 0;
		}
	}

	&-body {
		@media (max-width: 787px) {
			padding: 15px;
		}
	}

	&-info {
		.title {
			font-size: 1.125rem;
			font-weight: 700;
			margin-top: 28px;

			&-highlight {
				background: rgba(252, 194, 114, 0.53);
				text-align: center;
				padding: 10px 0;
				border-radius: 2px;
			}
		}
	}

	&-land {
		position: relative;
		padding: 0;
	}
}
.btn {
	&-orange {
		background: #faa831;
		text-align: center;
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
		height: 35px;
		// width: 100px;
		color: #fff;
		// margin: 15px 0 0;
		box-sizing: border-box;

		&:hover {
			border-color: #dc8300;
		}
	}
}

.btn {
	&-history {
		position: fixed;
		right: 0;
		top: 250px;
		z-index: 400;
		border-radius: 5px 0 0 5px;
		padding: 0.5rem 0.3rem;
	}
}
.row-payment {
	margin-top: 20px;
}

.row-payment-2 {
	margin-top: 0px;
}

.img_document_action {
	cursor: pointer;
	margin-top: 10px;
}
</style>
