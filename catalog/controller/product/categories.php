<?php

class ControllerProductCategories extends Controller {
	
	public function index() {

		$this->load->language('product/category');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

        $all_top_categories = $this->model_catalog_category->getCategories();

        $topcategories = array();

        // top level
        /*foreach ($all_top_categories as $result) {

			$topcategories[] = array(
		
				'category_id'		=> $result['category_id'],

				'name'				=> $result['name'],

				'href'				=> $this->url->link('product/category', 'path='.$result['category_id']),

				'second_categories' => array(),

			);

		}

		// second level
		for ($tp = 0; $tp < count($topcategories); $tp++) {
			$second_categories = $this->model_catalog_category->getCategories($topcategories[$tp]['category_id']);

			foreach ($second_categories as $sresult) {
				$topcategories[$tp]['second_categories'][] = array(

					'category_id' => $sresult['category_id'],

					'name' => $sresult['name'],

					'third_categories' => array(),

				);
			}
		}

		// third level
		for ($tp = 0; $tp < count($topcategories); $tp++) {
			$second_categories = $topcategories[$tp]['second_categories'];
			for ($sc = 0; $sc < count($second_categories); $sc++) {
				$third_categories = $this->model_catalog_category->getCategories($second_categories[$sc]['category_id']);

				foreach ($third_categories as $thresult) {
					// products
					$data['products'] = array();
					$filter_data = array(
    					'filter_category_id' => $thresult['category_id'],
					);
					$products = $this->model_catalog_product->getProducts($filter_data);

					$topcategories[$tp]['second_categories'][$sc]['third_categories'][] = array(

						'category_id' => $thresult['category_id'],

						'name'		  => $thresult['name'],

						'href'		  => $this->url->link('product/category', 'path='.$thresult['category_id']),

						'products'	  => $products

					);
				}
			}
		}*/

?>

<table>
	<thead>
		<th>cat level 1 name</th>
		<th>cat level 2 name</th>
		<th>cat level 3 name</th>
		<th>product sku</th>
		<th>product name</th>
		<th>url</th>
	</thead>
	<tbody>
		<?php
			$topcategories = $this->model_catalog_category->getCategories();
			for ($tp = 0; $tp < count($topcategories); $tp++) {
				echo '<tr>';
					echo '<td>' . $topcategories[$tp]['name'] . '</td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td>' . $this->url->link('product/category', 'path=' . $topcategories[$tp]['category_id']) . '</td>';
				echo '</tr>';

				$second_categories = $this->model_catalog_category->getCategories($topcategories[$tp]['category_id']);
				for ($sc = 0; $sc < count($second_categories); $sc++) {
					echo '<tr>';
						echo '<td></td>';
						echo '<td>' . $second_categories[$sc]['name'] . '</td>';
						echo '<td></td>';
						echo '<td></td>';
						echo '<td></td>';
						echo '<td></td>';
					echo '</tr>';

					$third_categories = $this->model_catalog_category->getCategories($second_categories[$sc]['category_id']);
					for ($th = 0; $th < count($third_categories); $th++) {
						echo '<tr>';
							echo '<td></td>';
							echo '<td></td>';
							echo '<td>' . $third_categories[$th]['name'] . '</td>';
							echo '<td></td>';
							echo '<td></td>';
							echo '<td>' . $this->url->link('product/category', 'path=' . $third_categories[$th]['category_id']) . '</td>';
						echo '</tr>';

						$data['products'] = array();
						$filter_data = array(
    						'filter_category_id' => $third_categories[$th]['category_id'],
						);
						$products = $this->model_catalog_product->getProducts($filter_data);
						foreach ($products as $key => $value) {
							$product_options = $this->model_catalog_product->getProductOptions($value['product_id']);
							// print_r($product_options);
							if (!empty($product_options)) {
								//foreach ($product_options as $option) {
								//	// $option_values = $this->model_catalog_option->getOptionValues($option['option_id']);
								//	foreach ($option['product_option_value'] as $valuep) {
								//		echo '<tr>';
								//			echo '<td></td>';
								//			echo '<td></td>';
								//			echo '<td></td>';
								//			echo '<td>' . $valuep['option_code_1'] . '</td>';
								//			echo '<td>' . $value['name'] . ' ' . $topcategories[$tp]['second_categories'][$sc]['third_categories'][$th]['name'] . '</td>';
								//			echo '<td>' . $this->url->link('product/product', 'product_id=' . $value['product_id']) . '</td>';
								//		echo '</tr>';
								//	}
								//}
								echo '<tr>';
									echo '<td></td>';
									echo '<td></td>';
									echo '<td></td>';
									echo '<td>' . $value['model'] . '</td>';
									echo '<td>' . $value['name'] . ' ' . $third_categories[$th]['name'] . '</td>';
									echo '<td>' . $this->url->link('product/product', 'product_id=' . $value['product_id']) . '</td>';
								echo '</tr>';								
							}// else {
							//	echo '<tr>';
							//		echo '<td></td>';
							//		echo '<td></td>';
							//		echo '<td></td>';
							//		echo '<td>' . $value['model'] . '</td>';
							//		echo '<td>' . $value['name'] . ' ' . $topcategories[$tp]['second_categories'][$sc]['third_categories'][$th]['name'] . '</td>';
							//		echo '<td>' . $this->url->link('product/product', 'product_id=' . $value['product_id']) . '</td>';
							//	echo '</tr>';
							//}
						}
					}
				}
			}
		?>
	</tbody>
</table>

<?php
		 // print_r($topcategories);

	}
}
?>

<style type="text/css">
table {
	width: 100%;
	border: 1px solid #000;
}

th, td {
	border: 1px solid #000;
	text-align: center;
}
</style>