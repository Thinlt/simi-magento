import React, { useCallback } from 'react';
import { arrayOf, func, number, shape, string } from 'prop-types';
import { Link } from 'src/drivers';

import { mergeClasses } from 'src/classify';
import getLocation from './getLocation';
import defaultClasses from './suggestedCategories.css';
import ReactHTMLParse from 'react-html-parser';

const SuggestedCategories = props => {
    const { categories, limit, onNavigate, value } = props;
    const classes = mergeClasses(defaultClasses, props.classes);

    const handleClick = useCallback(() => {
        if (typeof onNavigate === 'function') {
            onNavigate();
        }
    }, [onNavigate]);

    const items = categories
        .slice(0, limit)
        .map(({ label, value_string: categoryId }) => (
            <li key={categoryId} className={classes.item} >
                <Link
                    className={classes.link}
                    to={getLocation(value, categoryId)}
                    onClick={handleClick}
                >
                    <span className={classes.value} >{value}</span> <span> in </span> <span style={{textTransform: 'capitalize'}}>{`${ReactHTMLParse(label.toLowerCase())}`}</span>
                </Link>
            </li>
        ));

    return <ul className={classes.root}>{items}</ul>;
};

export default SuggestedCategories;

SuggestedCategories.defaultProps = {
    limit: 4
};

SuggestedCategories.propTypes = {
    categories: arrayOf(
        shape({
            label: string.isRequired,
            value_string: string.isRequired
        })
    ).isRequired,
    classes: shape({
        item: string,
        link: string,
        root: string,
        value: string
    }),
    limit: number.isRequired,
    onNavigate: func,
    value: string
};
