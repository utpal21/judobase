#!/usr/bin/ruby

require 'nokogiri'
require 'open-uri'
require 'net/http'
require 'uri'

api_url = "http://localhost/back/api/medicine/save_from_site"
home_url = "http://www.jah.ne.jp/~kako/"

indices = [
"cgi-bin/dwm_kana_disp2.cgi/~kako/?a&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?a&2",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?a&3",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?a&4",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?i&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?u&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?e&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?e&2",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?o&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ka&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ka&2",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ki&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ku&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ke&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ko&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ko&2",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?sa&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?sa&2",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?si&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?si&2",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?su&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?se&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?se&2",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?se&3",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?so&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ta&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ti&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?tu&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?te&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?te&2",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?to&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?to&2",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?na&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ni&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ne&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?no&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ha&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ha&2",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?hi&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?hi&2",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?hu&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?hu&2",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?hu&3",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?he&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?he&2",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ho&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ma&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?mi&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?mu&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?me&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?me&2",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?mo&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ya&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?yu&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?yo&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ra&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ri&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ri&2",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ru&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?re&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?ro&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?wa&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?AB&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?kanji&1",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?kanji&2",
"cgi-bin/dwm_kana_disp2.cgi/~kako/?kanji&3"
]

def get_html(url)
	is_windows = ((/cygwin|mswin|mingw|bccwin|wince|emx/ =~ RUBY_PLATFORM) != nil)

	tries = 3
	begin
		if is_windows
			uri = URI(url)
			html = Net::HTTP.get(uri.host, uri.path)
			html = html.encode("UTF-8", "Shift_JIS", :undef => :replace, :replace => "*")
		else	
			html = `wget -nv -q -O - \"#{url}\" | nkf -w`
		end
	rescue
		tries2 -= 1
		if tries2 > 0
			retry
		end
	end

	if html != nil
		html = html.gsub(/shift_jis/i, "utf-8")
	end
	
	return html
end

# loading index pages
for index_url in indices
	url = home_url + index_url
	puts url + "\n"
	tries = 3
	begin
		html = get_html(url)
		$idoc = Nokogiri::HTML(html)
	rescue
		tries -= 1
		if tries > 0
			retry
		end
	end
       
	next if $idoc == nil
	$idoc.css('table').each do | table |
		found = false
		table.css('th').each do | th |
			if th.content == '薬品名'
				found = true
				break;
			end
		end

		table.css('tr td a').each do | link |
			url = link['href']
			if url =~ /\/www.interq.or.jp\// 
				puts url
				$medicine_name = link.content
				# loading medicine pages	
				
				html = get_html(url)
				html = html.gsub(/<IMG[^>]+><BR>/i, "--TWONL--")
				html = html.gsub(/<BR>/i, "--NEWLINE--")
				html = html.gsub(/<LI>/i, "--LIMARK--")
				html = html.gsub(/<UL>/i, "")
				html = html.gsub(/<\/UL>/i, "")

				$doc = Nokogiri::HTML(html)
				found = false
				$doc.css('table tr').each  do | row |
					i = 0
					type = 0
					row.css('td').each do | cell |
						case i
						when 0
							ib = cell.css('b').first
							if ib != nil
								case ib.content 
								when "概説"
									type = 1
								when "副作用"
									type = 2
								end
							end
						when 1
							case type
							when 1
								$intro_text = cell.content
							when 2
								$side_effect = cell.content
								$side_effect = $side_effect.gsub(/--NEWLINE--/, "\n")
								$side_effect = $side_effect.gsub(/--LIMARK--/, "\n - ")
								$side_effect = $side_effect.gsub(/[\r\n]+/, "\n")
								$side_effect = $side_effect.gsub(/--TWONL--/, "\n")
								found = true
							end
						end
						i = i + 1
					end
				end

				if found
					uri = URI(api_url)
					res = Net::HTTP.post_form(uri, 'medicine_name' => $medicine_name, 'intro_text' => $intro_text, 'side_effect' => $side_effect)
					#puts res.body
				else
					puts "not found " + $medicine_name
				end
			end
		end
	end
end