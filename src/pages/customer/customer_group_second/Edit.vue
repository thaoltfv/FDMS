<template>
	<div id="UpdateNews" class="edit-category">
		<Form />
	</div>
</template>

<script>
import Form from "@/pages/customer/customer_group_second/Form";
import CustomerGroupSecond from "@/models/CustomerGroupSecond";
export default {
	name: "Edit",
	data() {
		return {
			detail: null
		};
	},
	components: {
		Form
	},
	beforeRouteEnter: async (to, from, next) => {
		const customer_group = await CustomerGroupSecond.find(to.query["id"]);
		to.meta["detail"] = customer_group.data;
		return next();
	}
};
</script>
<style scoped lang="scss"></style>
