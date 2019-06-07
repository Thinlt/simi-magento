import React from 'react'
import HtmlParser from 'react-html-parser'
//import Products from './Products'

class Innercontent extends React.Component {
    render = () => {
        let item = this.props.item
        if (!item || !item.entity_id)
            return ''
        let data = {}
        if (item.data && typeof item.data === 'string') {
            try {
                data = JSON.parse(item.data)
            } catch (err) {}
        } else if (item.data && typeof item.data === 'object') {
            data = item.data
        }
        if (item.type === 'button') {
            return item.name
        } else if (item.type === 'text') {
            return item.name
        } else if (item.type === 'image') { 
            if (data.image)
                return <img src={data.image} alt="pb img item" style={{width: '100%'}}/>
        } else if (item.type === 'category') { 
            return (
                <React.Fragment>
                    {data.image?<img src={data.image} alt="pb img item" style={{width: '100%'}}/>:''}
                    {item.name && <div style={{textAlign: 'center', marginTop: 10}}>{item.name}</div>}
                </React.Fragment>
            )
        } else if (item.type === 'product_scroll') { 
            return (
                <div className="product-scroll" style={{display: 'flex', flexWrap: 'wrap', overflow: 'hidden'}}>
                    <div style={{display: 'flex', width: '100%', marginBottom: 15, justifyContent: 'space-between'}}>
                        {item.name}
                        <div className="product-scroll-viewmore" style={{cursor: 'pointer'}} onClick={e => this.props.onClickItem(item, e, true)}>View more >></div>
                    </div>
                    <div  style={{display: 'flex', width: '100%', flexWrap: 'nowrap', overflow: 'auto'}}>
                        {/*<Products item={item} />*/}
                    </div>
                </div>
            )
        } else if (item.type === 'product_grid') { 
            return (
                <div style={{display: 'flex', flexWrap: 'wrap', overflow: 'hidden'}}>
                    <div style={{width: '100%', marginBottom: 10, textAlign: 'center'}}>
                        {item.name}
                    </div>
                    <div  style={{display: 'flex', width: '100%', flexWrap: 'wrap', justifyContent: 'start'}}>
                        {/*<Products item={item} /> */}
                    </div>
                </div>
            )
        } else if (item.type === 'paragraph') {
            if (data.paragraphContent)
                return HtmlParser(data.paragraphContent)
        }
        return ''
    }
}

export default Innercontent