import React, { Component } from 'react';
import { arrayOf, func, object, shape, string } from 'prop-types';
import TileList from './tileList';
import ListItemNested from 'src/simi/App/Bianca/BaseComponents/MuiListItem/Nested';
import List from 'src/simi/App/Bianca/Components/Product/ProductFullDetail/Options/ConfigurableOptions/List';
import Identify from 'src/simi/Helper/Identify';
import QuestionIcon from 'src/simi/App/Bianca/BaseComponents/Icon/Question';
require('./option.scss');

const getItemKey = ({ value_index }) => value_index;

class Option extends Component {

	constructor(props) {
		super(props);
		this.state = {optionKey: null}
	}
	static propTypes = {
		attribute_id: string,
		attribute_code: string.isRequired,
		label: string.isRequired,
		onSelectionChange: func,
		onAskOption: func,
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
			if (this.refListItem) {
				this.refListItem.closeMenu();
			}
			this.setState({optionKey});
		}
	};

	handleAskOption = () => {
		const { attribute_id, attribute_code, onAskOption } = this.props;
		onAskOption(attribute_id, attribute_code);
	}

	createRefListItem = (obj) => {
		this.refListItem = obj;
	}

	render() {
		const { handleSelectionChange, handleAskOption, props } = this;
		const { attribute_code, label, values, attribute_id } = props;
		if (attribute_code === 'size') {
			return (
				<div className="option-root">
					<h3 className="option-title">
						<span>{label}</span>
					</h3>

					<div className="list-size">
						<div className="option-select">
							<ListItemNested
								obj={this.createRefListItem}
								primarytext={<div className="choose-your-size">{Identify.__('Choose your size...')}</div>}
							>
								<List
									getItemKey={getItemKey}
									items={values}
									onSelectionChange={handleSelectionChange}
									attribute_code={attribute_code}
									attribute_id={attribute_id}
									defaultSelection={this.state.optionKey}
								/>
							</ListItemNested>
						</div>
						<div className="size-guide"><button onClick={handleAskOption}><QuestionIcon />{Identify.__('Size Guide')}</button></div>
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
