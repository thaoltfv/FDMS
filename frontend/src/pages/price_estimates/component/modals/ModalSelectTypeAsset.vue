<template>
	<div
		@click.self="handleCancel"
		class="modal-purpose d-flex justify-content-center align-items-center"
	>
		<div class="card card__show">
			<div class="card-header">
				<div class="title">
					Chọn loại tài sản
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col">
						<button
							type="button"
							class="btn btn-purpose h-100"
							:class="land ? 'active' : ''"
							@click="selectLand"
						>
							Đất trống
						</button>
					</div>
					<div class="col">
						<button
							type="button"
							class="btn btn-purpose h-100"
							:class="landHouse ? 'active' : ''"
							@click="selectLandHaveHouse"
						>
							Đất có nhà
						</button>
					</div>
				</div>
				<div class="container__selected">
					<button
						type="button"
						class="btn btn-select"
						:class="!landHouse && !land ? 'disabled' : ''"
						@click="handleAction"
					>
						Chọn
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: "ModalSelectTypeAsset",
	data() {
		return {
			landHouse: false,
			land: false
		};
	},
	methods: {
		selectLandHaveHouse() {
			this.land = false;
			this.landHouse = true;
		},
		selectLand() {
			this.landHouse = false;
			this.land = true;
		},
		handleCancel(event) {
			this.$emit("cancel", event);
		},
		handleAction(event) {
			let routeData = {};
			if (this.land) {
				routeData = this.$router.resolve({
					name: "warehouse.create",
					query: { asset_type_id: 37 }
				});
			} else {
				routeData = this.$router.resolve({
					name: "warehouse.create",
					query: { asset_type_id: 38 }
				});
			}
			this.handleCancel(event);
			window.open(routeData.href, "_blank");
		}
	}
};
</script>

<style lang="scss" scoped>
.modal-purpose {
	position: fixed;
	z-index: 1005;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.6);

	@media (max-width: 787px) {
		padding: 20px;
	}
	.card {
		background-image: url("../../../../assets/images/im_popup.png");
		background-repeat: no-repeat;
		background-size: contain;
		box-shadow: 0 0 5px rgba(0, 0, 0, 0.25);
		max-width: 40rem;
		width: 100%;
		margin-bottom: 0;
		border-radius: 5px;
		opacity: 0;
		transition: 0.3s;
		&.card__show {
			opacity: 1;
		}
		&-header {
			padding: 1.5rem 0;
			border-bottom: none;
			display: flex;
			justify-content: center;
			.title {
				font-size: 20px;
				text-transform: uppercase;
				color: #f28c1c;
				font-weight: 700;
			}
			h3 {
				color: #333333;
			}
			img {
				cursor: pointer;
			}
		}
		&-body {
			text-align: center;
			padding: 6px 34px 34px;
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
.btn {
	&-orange {
		background: #faa831;
		color: #ffffff;
		font-weight: bold !important;
	}
	&-purpose {
		width: 100%;
		margin-bottom: 15px;
		background: #f5f5f5;
		color: #555555;
		border: 1px solid #e7e7e7;
		box-sizing: border-box;
		transition: 0.3s;
		&.active {
			color: #ffffff !important;
			background: #f28c1c !important;
			&:hover {
				box-shadow: none !important;
			}
		}
		&:hover {
			box-shadow: 0 3px 2px rgba(0, 0, 0, 0.3) !important;
		}
	}
	&-select {
		background: #f28c1c;
		color: #ffffff;
		&.disabled {
			background: #ffffff;
			color: #000000;
			border: 1px solid #e7e7e7;
			box-sizing: border-box;
			cursor: not-allowed;
			opacity: 1;
		}
	}
}
.container {
	&__selected {
		display: flex;
		justify-content: flex-end;
		margin-top: 40px;
	}
}
.title_content {
	text-align: left;
}
</style>
