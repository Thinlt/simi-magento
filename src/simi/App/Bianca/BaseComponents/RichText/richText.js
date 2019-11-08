import React, { Component } from 'react';

const toHTML = str => ({ __html: str });

class RichText extends Component {
    render() {
        const { className, content } = this.props;

        return (
            <div
                className={className}
                dangerouslySetInnerHTML={toHTML(content)}
            />
        );
    }
}

export default RichText;
