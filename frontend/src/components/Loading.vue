<template>
	<div class="loading-overlay">
		<div class="loading-content">
			<div class="loading-spinner"></div>
			<p class="loading-text">
				<span>Đang xử lý, vui lòng đợi</span>
				<span class="dot">.</span>
				<span class="dot">.</span>
				<span class="dot">.</span>
			</p>
		</div>
	</div>
</template>

<script>
export default {
	name: "Loading",
};
</script>

<style scoped>
.loading-overlay {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(0, 0, 0, 0.5);
	display: flex;
	justify-content: center;
	align-items: center;
	z-index: 999999;
}

.loading-spinner {
	/* border: 5px solid #f3f3f3;
  border-top: 5px solid #3498db;
  border-radius: 50%; */
	width: 100px;
	height: 100px;
	animation: spin 1s linear infinite;
	background-image: url("../assets/images/company_logo.png");
	background-repeat: no-repeat;
	background-position: center;
	background-size: contain;
}

.loading-content {
	display: flex;
	flex-direction: column;
	align-items: center;
	text-align: center;
}

.loading-text {
	margin-top: 10px;
	font-size: 20px;
	color: white;
}

.dot {
	animation: blink 1s infinite steps(1);
}

@keyframes blink {
	50% {
		opacity: 0;
	}
}

@keyframes spin {
	0% {
		transform: rotate(0deg);
	}
	100% {
		transform: rotate(360deg);
	}
}
</style>
