;var jQueryWeatherConfig = {
	lang : {
		day : '白天',
		night : '夜晚',
		temp : '°C',
		wind : '级风',
	},
	convert : function(sky){
		var weatherInfo = {
				cloudy 		: ['多云','多云转阴','晴转多云','阴转多云'],
				overcast	: ['阴','雾','沙尘暴','浮尘','扬沙','强沙尘暴'],
				rainy		: ['多云转小雨','小雨转多云','小雨','中雨','大雨','暴雨','大暴雨','特大暴雨','冻雨','小雨转中雨','中雨转大雨','大雨转暴雨','暴雨转大暴雨','大暴雨转特大暴雨','阵雨','雷阵雨','雷阵雨伴有冰雹'],
				sleet		: ['雨夹雪'],
				snow		: ['阵雪','小雪','中雪','大雪','暴雪','小雪转中雪','中雪转大雪','大雪转暴雪','中雪转小雪','大雪转中雪','暴雪转大雪'],
				sunshine	: ['晴']
			},
			weather = '',
			state = '';
		for( state in weatherInfo ){
			if( $.inArray( sky , weatherInfo[state] ) > -1 ){
				weather = state;
				break;
			}
		} 
		return weather || state || 'sunshine'  ;
	}
};