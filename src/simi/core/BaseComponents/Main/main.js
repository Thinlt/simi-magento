import React, { Component } from 'react';
import { bool, shape, string } from 'prop-types';

import classify from 'src/classify';
import Header from 'src/simi/core/BaseComponents/Header';
import defaultClasses from './main.css';

class Main extends Component {
    static propTypes = {
        classes: shape({
            page: string,
            page_masked: string,
            root: string,
            root_masked: string
        }),
        isMasked: bool
    };

    get classes() {
        const { classes, isMasked } = this.props;
        const suffix = isMasked ? '_masked' : '';

        return ['page', 'root'].reduce(
            (acc, val) => ({ ...acc, [val]: classes[`${val}${suffix}`] }),
            {}
        );
    }

    render() {
        const { classes, props } = this;
        const { children } = props;
        console.log(this.props)
        return (
            <main className={classes.root}>
                <Header />
                <div id="data-breadcrumb"/>
                <div className={classes.page}>{children}</div>
                <div>footer</div>
            </main>
        );
    }
}

export default classify(defaultClasses)(Main);