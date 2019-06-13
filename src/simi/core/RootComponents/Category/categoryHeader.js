import React from 'react'

const CategoryHeader = props => {
    const { classes, name, image_url } = props
    return (
        <div className={classes['category-header']}>
            <img
                alt={name}
                src={image_url}
            />
        </div>
    )
}

export default CategoryHeader