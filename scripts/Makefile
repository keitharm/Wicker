CC=g++
CFLAGS=-c -Wall
LDFLAGS=
SOURCES=wicker.cpp AP.cpp CapFile.cpp Client.cpp Config.cpp
OBJECTS=$(SOURCES:.cpp=.o)
EXECUTABLE=wicker

all: $(SOURCES) $(EXECUTABLE)
	
$(EXECUTABLE): $(OBJECTS) 
	$(CC) $(LDFLAGS) $(OBJECTS) -o $@

.cpp.o:
	$(CC) $(CFLAGS) $< -o $@

clean:
	rm -rf *.o wicker
