import React, { Component } from 'react';
import { arrayOf, func, object, shape, string } from 'prop-types';
import TileList from './tileList';
import ListItemNested from 'src/simi/App/Bianca/BaseComponents/MuiListItem/Nested';
import Identify from 'src/simi/Helper/Identify';
require('./option.scss');

const getItemKey = ({ value_index }) => value_index;

class Option extends Component {
	constructor(props) {
		super(props);
	}
	static propTypes = {
		attribute_id: string,
		attribute_code: string.isRequired,
		label: string.isRequired,
		onSelectionChange: func,
		values: arrayOf(object).isRequired
	};

	handleSelectionChange = (selection) => {
		const { attribute_id, onSelectionChange } = this.props;
		if (onSelectionChange) {
			var optionKey = ''
			for(var [key, value] of selection.entries()){
				optionKey = key
			}
			if(this.props.attribute_code === "size"){
				for(let i = 0; i< this.props.values.length; i++){
					if(this.props.values[i].value_index === optionKey){
						$('.choose-your-size').html(this.props.values[i].label)
						break
					}
				}
			}
			onSelectionChange(attribute_id, selection);
		}
	};

	render() {
		const { handleSelectionChange, props } = this;
		const { attribute_code, label, values } = props;
		if (attribute_code === 'size') {
			return (
				<div className="option-root">
					<h3 className="option-title">
						<span>{label}</span>
					</h3>

					<div className="list-size">
						<ListItemNested
							primarytext={<div className="choose-your-size">{Identify.__('Choose your size...')}</div>}
						>
							<TileList
								getItemKey={getItemKey}
								items={values}
								onSelectionChange={handleSelectionChange}
								attribute_code={attribute_code}
							/>
						</ListItemNested>
					</div>
				</div>
			);
		} else if (attribute_code === 'color') {
			return (
				<div className="option-root">
					<h3 className="option-title">
						<span>{label}</span>
					</h3>
					<div className="list-color">
						<TileList getItemKey={getItemKey} items={values} onSelectionChange={handleSelectionChange} attribute_code={attribute_code} />
					</div>
				</div>
			);
		} else {
			return (
				<div className="option-root">
					<h3 className="option-title">
						<span>{label}</span>
					</h3>
					<TileList getItemKey={getItemKey} items={values} onSelectionChange={handleSelectionChange} attribute_code={attribute_code}/>
				</div>
			);
		}
	}
}

export default Option;
