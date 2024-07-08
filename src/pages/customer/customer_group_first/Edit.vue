<template>
	<div id="UpdateNews" class="edit-category">
		<Form />
	</div>
</template>

<script>
import Form from "@/pages/customer/customer_group_first/Form";
import CustomerGroupFirst from "@/models/CustomerGroupFirst";
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
		const category = await CustomerGroupFirst.find(to.query["id"]);
		to.meta["detail"] = category.data;
		return next();
	}
};
</script>
<style scoped lang="scss"></style>
