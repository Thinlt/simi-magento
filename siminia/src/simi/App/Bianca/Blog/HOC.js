import React from 'react'
import {LazyComponent} from "src/simi/BaseComponents/Async";

export const LazyBlog = props => {
    return <LazyComponent component={()=>import(/* webpackChunkName : "Blog" */'./index')} {...props}/>
}

export const LazyPost = props => {
    return <LazyComponent component={()=>import(/* webpackChunkName : "BlogArticle" */'./post')} {...props}/>
}