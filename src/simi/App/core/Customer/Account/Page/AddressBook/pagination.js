import React from 'react';
import PropTypes from 'prop-types';

class Pagination extends React.Component {

    static defaultProps = {
        hideOnSinglePage: false,
        className: '',
        onChange: ()=>{},
        dispatch: ()=>{},
        current: 1,
        pageNumber: 5,
        pageSize: 10,
        pageSizeOptions: [5, 10, 15, 20],
        showSizeOptions: true,
        showPrevNext: true,
        showJumper: true,
        showInfo: true,
        prevIcon: ()=>{return '<'},
        nextIcon: ()=>{return '>'},
        jumpPrevIcon: ()=>{return '...'},
        jumpNextIcon: ()=>{return '...'},
        style: {},
        dataItems: []
    }

    static propTypes = {
        hideOnSinglePage: PropTypes.bool,
        className: PropTypes.string,
        onChange: PropTypes.func,
        dispatch: PropTypes.func,
        current: PropTypes.number,
        pageNumber: PropTypes.number,
        pageSize: PropTypes.number,
        pageSizeOptions: PropTypes.arrayOf(PropTypes.number),
        showSizeOptions: PropTypes.bool,
        showPrevNext: PropTypes.bool,
        showJumper: PropTypes.bool,
        showInfo: PropTypes.bool,
        prevIcon: PropTypes.oneOfType([PropTypes.func, PropTypes.node]),
        nextIcon: PropTypes.oneOfType([PropTypes.func, PropTypes.node]),
        jumpPrevIcon: PropTypes.oneOfType([PropTypes.func, PropTypes.node]),
        jumpNextIcon: PropTypes.oneOfType([PropTypes.func, PropTypes.node]),
        style: PropTypes.object,
        dataItems: PropTypes.array
    }
    
    constructor(props) {
        super(props);

        let current = props.current;
        let pageSize = props.pageSize;
        let allPages = this.calculatePageNumber();
        let prevPage = current - 1 > 0 ? current - 1 : 0;
        let nextPage = current + 1 < allPages ? current + 1 : allPages;
        const { pageFrom, pageTo }  = this.calculatePageFromTo(current, pageSize)

        this.state = {
            current,
            pageSize,
            allPages,
            prevPage,
            nextPage,
            pageTo,
            pageFrom
        };
        
    }

    componentWillMount() {
        const props = this.props;
        const {items} = this.getItems();
        props.dispatch({items: items});
    }

    calculatePageNumber() {
        const total = this.props.dataItems.length
        let pageSize = this.props.pageSize
        if (this.state && this.state.hasOwnProperty('pageSize')) {
            pageSize = this.state.pageSize
        }
        return (Math.floor(total / pageSize) + 1);
    }

    calculatePageFromTo(current, pageSize) {
        let pageTo  = current * pageSize
        let pageFrom = (pageTo - pageSize) < 1 ? 1 : (pageTo - pageSize + 1)
        return {pageFrom, pageTo}
    }

    gotoPage = (p) => {
        const newState = this.dispatchChangeEvent({current: p})
        this.setState(newState)
    }

    optionsHandle = (e) => {
        const size = parseInt(e.target.value)
        const newState = this.dispatchChangeEvent({pageSize: size})
        this.setState(newState)
    }

    getItems() {
        const { dataItems } = this.props
        const { current, pageSize} = this.state
        const { pageFrom, pageTo }  = this.calculatePageFromTo(current, pageSize)
        let items = dataItems.slice(pageFrom - 1, pageTo)
        return {items, pageFrom, pageTo}
    }

    dispatchChangeEvent = (stateChange) => {
        const {items, pageFrom, pageTo} = this.getItems()
        let newState = {...this.state, ...stateChange, ...{pageFrom: pageFrom, pageTo: pageTo}}
        this.props.onChange(items, newState, this.props)
        this.props.dispatch({items: items})
    }

    renderPages() {
        const props = this.props
        const { current, allPages, prevPage, nextPage } = this.state
        const pageNumber = this.props.pageNumber;
        let calcFirstPager = current - Math.floor(pageNumber / 2);
        let calcLastPager = current + Math.floor(pageNumber / 2);
        let firstPager = calcFirstPager > 1 ? calcFirstPager : 1 ;
        let lastPager = calcLastPager <= allPages ? calcLastPager : allPages;
        let jumpPrev = (firstPager - pageNumber) >= 1 ? firstPager - pageNumber : 1;
        let jumpNext = (lastPager + pageNumber) <= allPages ? lastPager + pageNumber : allPages;

        let pages = []
        for (let i=firstPager; i<=lastPager; i++) {
            pages.push(i)
        }

        if (pages.length < 1) {
            return null
        }

        return (
            <>
            {allPages > 1 && 
                <ul>
                    {props.showPrevNext ? 
                        <li><a href="" onClick={(e)=> {this.gotoPage(prevPage); e.preventDefault()}}>{typeof props.prevIcon === 'function' ? props.prevIcon():props.prevIcon}</a></li>
                        : null
                    }
                    {props.showJumper && jumpPrev < firstPager ? 
                        <li><a href="" onClick={(e)=> {this.gotoPage(jumpPrev); e.preventDefault()}}>{typeof props.jumpPrevIcon === 'function' ? props.jumpPrevIcon():props.jumpPrevIcon}</a></li>
                        : null
                    }

                    {pages.map((page, index) => {
                        return <li key={index}><a href="" onClick={(e)=> {this.gotoPage(page); e.preventDefault()}}>{page}</a></li>
                    })}

                    {props.showJumper && jumpNext < lastPager ? 
                        <li><a href="" onClick={(e)=> {this.gotoPage(jumpNext); e.preventDefault()}}>{typeof props.jumpNextIcon === 'function' ? props.jumpNextIcon():props.jumpNextIcon}</a></li>
                        : null
                    }
                    {props.showPrevNext ? 
                        <li><a href="" onClick={(e)=> {this.gotoPage(nextPage); e.preventDefault()}}>{typeof props.nextIcon === 'function' ? props.nextIcon():props.nextIcon}</a></li>
                        : null
                    }
                </ul>
            }
            </>
        );
    }

    renderInfo() {
        const { dataItems, showInfo } = this.props
        const { current, pageSize } = this.state
        const size = dataItems.length
        const {pageFrom, pageTo} = this.calculatePageFromTo(current, pageSize)
        if (!showInfo) {
            return null
        }
        return (
            <div className="info"><span>Items {pageFrom} - {pageTo < size ? pageTo : size} of {size}</span></div>
        );
    }

    renderOptions() {
        const { pageSizeOptions, showSizeOptions } = this.props
        const { pageSize } = this.state
        if (!showSizeOptions) {
            return null
        }
        return (
            <div className="options-size">
                <span>Show</span>
                <select onChange={this.optionsHandle}>
                    {
                        pageSizeOptions.map((size, index)=>{
                            return <option value={size} defaultValue={pageSize} key={index}>{size}</option>
                        })
                    }
                </select>
                <span>per page</span>
            </div>
        );
    }

    render() {
        const props = this.props;
        return (
            <div className={props.className} style={props.style}>
                {this.renderPages()}
                {this.renderInfo()}
                {this.renderOptions()}
            </div>
        );
    }
}

export default Pagination;
