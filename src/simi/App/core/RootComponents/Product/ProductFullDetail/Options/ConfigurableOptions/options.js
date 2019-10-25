import React, { Component } from 'react';
import { arrayOf, func, shape, string } from 'prop-types';

import Option from './option';

class Options extends Component {
    static propTypes = {
        onSelectionChange: func,
        options: arrayOf(
            shape({
                attribute_id: string.isRequired
            })
        ).isRequired
    };

    handleSelectionChange = (optionId, selection) => {
        const { onSelectionChange } = this.props;

        if (onSelectionChange) {
            onSelectionChange(optionId, selection);
        }
    };

    render() {
        const { handleSelectionChange, props } = this;
        const { options } = props;
        // Move size on above color
        const reverseOptions = []
        reverseOptions.push(options[1])
        reverseOptions.push(options[0])
        return reverseOptions.map(option => (
            <Option
                {...option}
                key={option.attribute_id}
                onSelectionChange={handleSelectionChange}
            />
        ));
    }
}

export default Options;
