			<tr>
				<th scope="row">
					<label><?php esc_html_e('Ratings', 'W2MB'); ?></label>
				</th>
				<td>
					<input
						name="ratings_enabled"
						type="checkbox"
						value="1"
						<?php if (isset($level->ratings_enabled)) checked($level->ratings_enabled, 1, true); ?> />
				</td>
			</tr>