import React, { Component, Fragment } from 'react';
// import { arrayOf, node, number, oneOfType, shape, string } from 'prop-types';
import Icon from 'src/components/Icon';
import ChevronDownIcon from 'react-feather/dist/icons/chevron-down';
import { FieldIcons } from 'src/components/Field';
const arrow = <Icon src={ChevronDownIcon} size={18} />;

class Select extends Component {
    render() {
        const { items, className, ...rest } = this.props;
        if (!items || !items.length) return null;

        const options = items.map(({ label, value }) => (
            <option key={value} value={value}>
                {label || (value != null ? value : '')}
            </option>
        ));

        return (
            <Fragment>
                <FieldIcons after={arrow}>
                    <select className={className} {...rest} >
                        {options}
                    </select>
                </FieldIcons>
            </Fragment>
        );
    }
}

export default Select;
