 import React from 'react';
 import PropTypes from 'prop-types';
 require ('./index.scss')
 
 const $ = window.$
class SocialShare extends React.PureComponent{
    constructor(props) {
        super(props);
        this.state = {
            reRenderSuccess : "not-render-yet"
        }
    }
    componentDidMount(){
        let renderStatus = this.state.reRenderSuccess
        let url = this.props.sharingUrl?this.props.sharingUrl:document.URL
        if(url.indexOf('?id') === -1 && this.props.id){
            url = url + '?id='+this.props.id;
        }
        $(function () {
            const social = $('#social-share');
            social.attr('data-url',url);
            $('.social-share').html(social.clone());
            const btn = $('.social-share .at-share-btn-elements').children('a');
            btn.each(function () {
                $(this).click(function () {
                    const a = $(this).attr('class');
                    a = 'a.' + a.split(" ")[2];
                    a = $('#social-share').find(a);
                    a[0].click();
                })
            })
        })
    }

    updateDesign(){
        $('.social-share .at-share-btn-elements .at-icon-wrapper span.at-icon-wrapper svg').css("display", "none")
        $('.social-share .at-share-btn-elements a.at-svc-facebook span.at-icon-wrapper').append(
            '<div class="fb"><span class="share-facebook" aria-hidden="true"></span></div>'
        )
        $('.social-share .at-share-btn-elements a.at-svc-twitter span.at-icon-wrapper').append(
            '<div class="twitter"><span class="share-twitter" aria-hidden="true"></span></div>'
        )
        $('.social-share .at-share-btn-elements a.at-svc-google span.at-icon-wrapper').append(
            '<div class="google"><span class="share-google" aria-hidden="true"></span></div>'
        )
        $('.social-share .at-share-btn-elements a.at-svc-linkedin span.at-icon-wrapper').append(
            '<div class="linkedin"><span class="share-linkedin" aria-hidden="true"></span></div>'
        )
    }

    renderJS(){
        var self = this
        this.start_time = new Date().getTime();
        $.ajax({
            method: "GET",
            cache: false,
            url: "//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5da533f6b678dc11",
            dataType: "script",
            start_time: new Date().getTime(),
            success: function(res){
                // wait ajax return response success 
                /* var along = new Date().getTime() - self.start_time
                let count = 0;
                // console.log(('This request took '+along+' ms'));
                setTimeout(function(){
                    count++;
                    // change state to re-render and update our customize design
                    self.setState({reRenderSuccess: "already-re-rendered"})
                    if (count===1){
                        // only update design 1 time
                        // self.updateDesign()
                    }
                },along); */
            }
        });
    }
 
    render(){
        return(
            <div className={this.props.className}>
                <div className="social-share">
                    <div className="at-share-btn-elements">
                        <div className="at-icon-wrapper">
                            <span className="at-icon-wrapper">
                                <div className="fb"><span className="share-facebook" aria-hidden="true"></span></div>
                                <div className="twitter"><span className="share-twitter" aria-hidden="true"></span></div>
                                <div className="google"><span className="share-google" aria-hidden="true"></span></div>
                                <div className="linkedin"><span className="share-linkedin" aria-hidden="true"></span></div>
                            </span>
                        </div>
                    </div>
                </div>
                <div className="social-script"></div>
                {this.renderJS()}
            </div>
        )
    }
}

SocialShare.propTypes = {
    className: PropTypes.string
}

SocialShare.defaultProps = {
    className: ''
}

 export default SocialShare;